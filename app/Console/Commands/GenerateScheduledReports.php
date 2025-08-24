<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;

class GenerateScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-scheduled 
                            {--force : Force generation of all scheduled reports}
                            {--report= : Generate specific report by ID}
                            {--user= : Generate reports for specific user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate scheduled reports and send them via email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scheduled report generation...');
        
        $force = $this->option('force');
        $specificReport = $this->option('report');
        $specificUser = $this->option('user');
        
        // Get reports that need to be generated
        $query = Report::where('is_scheduled', true)
                      ->where('status', 'active');
        
        if ($specificReport) {
            $query->where('id', $specificReport);
        }
        
        if ($specificUser) {
            $query->where('user_id', $specificUser);
        }
        
        if (!$force) {
            // Only get reports that are due for generation
            $query->where(function($q) {
                $q->whereNull('next_generation_at')
                  ->orWhere('next_generation_at', '<=', now());
            });
        }
        
        $reports = $query->get();
        
        if ($reports->isEmpty()) {
            $this->info('No scheduled reports found for generation.');
            return 0;
        }
        
        $this->info("Found {$reports->count()} reports to generate.");
        
        $progressBar = $this->output->createProgressBar($reports->count());
        $progressBar->start();
        
        $generated = 0;
        $failed = 0;
        
        foreach ($reports as $report) {
            try {
                $this->generateReport($report);
                $generated++;
                $this->line("\n✓ Generated: {$report->name} (ID: {$report->id})");
            } catch (\Exception $e) {
                $failed++;
                $this->error("\n✗ Failed: {$report->name} (ID: {$report->id}) - {$e->getMessage()}");
                
                // Log the error
                \Log::error('Scheduled report generation failed', [
                    'report_id' => $report->id,
                    'report_name' => $report->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->newLine(2);
        $this->info("Report generation completed!");
        $this->info("Generated: {$generated}");
        
        if ($failed > 0) {
            $this->error("Failed: {$failed}");
        }
        
        return 0;
    }
    
    /**
     * Generate a specific report.
     */
    private function generateReport(Report $report)
    {
        $this->line("\nGenerating report: {$report->name}");
        
        // Generate report data
        $reportData = $report->generateData();
        
        // Update report tracking
        $report->updateGenerationTracking();
        
        // Calculate next generation time
        $report->next_generation_at = $this->calculateNextGeneration(
            $report->schedule_frequency,
            $report->schedule_time
        );
        $report->save();
        
        // Store the result
        $report->update(['last_result' => $reportData]);
        
        // Send email if configured
        if ($report->auto_email && !empty($report->email_recipients)) {
            $this->sendReportEmail($report, $reportData);
        }
        
        $this->line("  ✓ Report data generated");
        
        if ($report->auto_email) {
            $this->line("  ✓ Email sent to " . count($report->email_recipients) . " recipients");
        }
    }
    
    /**
     * Send report via email.
     */
    private function sendReportEmail(Report $report, array $reportData)
    {
        // Generate PDF for email attachment
        $pdf = PDF::loadView('reports.pdf', compact('report', 'reportData'));
        $pdfContent = $pdf->output();
        
        $filename = 'report-' . \Str::slug($report->name) . '-' . now()->format('Y-m-d') . '.pdf';
        
        // Store PDF temporarily
        $tempPath = 'temp/reports/' . $filename;
        Storage::put($tempPath, $pdfContent);
        
        try {
            foreach ($report->email_recipients as $email) {
                Mail::send('emails.scheduled-report', compact('report', 'reportData'), function ($message) use ($report, $email, $tempPath, $filename) {
                    $message->to($email)
                           ->subject('Scheduled Report: ' . $report->name)
                           ->attach(Storage::path($tempPath), [
                               'as' => $filename,
                               'mime' => 'application/pdf'
                           ]);
                });
            }
        } finally {
            // Clean up temporary file
            if (Storage::exists($tempPath)) {
                Storage::delete($tempPath);
            }
        }
    }
    
    /**
     * Calculate next generation time.
     */
    private function calculateNextGeneration($frequency, $time)
    {
        $now = Carbon::now();
        
        switch ($frequency) {
            case 'daily':
                return $now->addDay()->setTimeFromTimeString($time);
            case 'weekly':
                return $now->addWeek()->setTimeFromTimeString($time);
            case 'monthly':
                return $now->addMonth()->setTimeFromTimeString($time);
            case 'quarterly':
                return $now->addQuarter()->setTimeFromTimeString($time);
            case 'yearly':
                return $now->addYear()->setTimeFromTimeString($time);
            default:
                return null;
        }
    }
}