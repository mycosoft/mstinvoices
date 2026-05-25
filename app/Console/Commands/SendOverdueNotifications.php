<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\PaymentNotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendOverdueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-overdue {--days=1 : Number of days overdue to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send overdue payment notifications for invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $overdueDate = Carbon::now()->subDays($days);
        
        $this->info("Sending overdue notifications for invoices overdue since {$overdueDate->format('Y-m-d')}...");
        
        // Find overdue invoices
        $invoices = Invoice::where('due_date', '<', $overdueDate)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->with(['client', 'user'])
            ->get();
        
        if ($invoices->isEmpty()) {
            $this->info('No overdue invoices found.');
            return;
        }
        
        $this->info("Found {$invoices->count()} overdue invoices to send notifications for.");
        
        $notificationService = new PaymentNotificationService();
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($invoices as $invoice) {
            try {
                $this->line("Sending overdue notification for invoice {$invoice->invoice_number} to {$invoice->client->email}...");
                
                if ($notificationService->sendOverduePaymentNotification($invoice)) {
                    $successCount++;
                    $this->info("✓ Overdue notification sent successfully for invoice {$invoice->invoice_number}");
                } else {
                    $errorCount++;
                    $this->error("✗ Failed to send overdue notification for invoice {$invoice->invoice_number}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Error sending overdue notification for invoice {$invoice->invoice_number}: " . $e->getMessage());
            }
        }
        
        $this->info("Overdue notifications completed: {$successCount} successful, {$errorCount} failed.");
    }
}
