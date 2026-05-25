<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\PaymentNotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders {--days=3 : Number of days before due date to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders for invoices due soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $reminderDate = Carbon::now()->addDays($days);
        
        $this->info("Sending payment reminders for invoices due on {$reminderDate->format('Y-m-d')}...");
        
        // Find invoices due in the specified number of days
        $invoices = Invoice::where('due_date', $reminderDate)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->with(['client', 'user'])
            ->get();
        
        if ($invoices->isEmpty()) {
            $this->info('No invoices found for reminder date.');
            return;
        }
        
        $this->info("Found {$invoices->count()} invoices to send reminders for.");
        
        $notificationService = new PaymentNotificationService();
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($invoices as $invoice) {
            try {
                $this->line("Sending reminder for invoice {$invoice->invoice_number} to {$invoice->client->email}...");
                
                if ($notificationService->sendPaymentReminderNotification($invoice)) {
                    $successCount++;
                    $this->info("✓ Reminder sent successfully for invoice {$invoice->invoice_number}");
                } else {
                    $errorCount++;
                    $this->error("✗ Failed to send reminder for invoice {$invoice->invoice_number}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Error sending reminder for invoice {$invoice->invoice_number}: " . $e->getMessage());
            }
        }
        
        $this->info("Payment reminders completed: {$successCount} successful, {$errorCount} failed.");
    }
}
