<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\PaymentNotificationService;
use Illuminate\Console\Command;

class TestPaymentNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:test-notifications {invoice_id? : Invoice ID to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test payment notification emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceId = $this->argument('invoice_id');
        
        if ($invoiceId) {
            $invoice = Invoice::with(['client', 'user', 'payments'])->find($invoiceId);
            if (!$invoice) {
                $this->error("Invoice with ID {$invoiceId} not found.");
                return;
            }
        } else {
            // Find the first unpaid invoice
            $invoice = Invoice::with(['client', 'user', 'payments'])
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->first();
            
            if (!$invoice) {
                $this->error('No unpaid invoices found to test with.');
                return;
            }
        }
        
        $this->info("Testing payment notifications with invoice: {$invoice->invoice_number}");
        $this->info("Client: {$invoice->client->display_name} ({$invoice->client->email})");
        $this->info("Total Amount: {$invoice->total_amount}");
        $this->info("Balance Due: {$invoice->balance_due}");
        
        $notificationService = new PaymentNotificationService();
        
        // Create a test payment first
        $testPayment = $invoice->payments()->create([
            'amount' => 100.00,
            'payment_date' => now(),
            'payment_method' => 'cash',
            'notes' => 'Test payment',
            'created_by' => $invoice->user_id
        ]);
        
        // Test payment received notification
        $this->info("\n1. Testing Payment Received Notification...");
        if ($notificationService->sendPaymentReceivedNotification($invoice, $testPayment)) {
            $this->info("✓ Payment received notification sent successfully");
        } else {
            $this->error("✗ Failed to send payment received notification");
        }
        
        // Test payment reminder notification
        $this->info("\n2. Testing Payment Reminder Notification...");
        if ($notificationService->sendPaymentReminderNotification($invoice)) {
            $this->info("✓ Payment reminder notification sent successfully");
        } else {
            $this->error("✗ Failed to send payment reminder notification");
        }
        
        // Test overdue payment notification
        $this->info("\n3. Testing Overdue Payment Notification...");
        if ($notificationService->sendOverduePaymentNotification($invoice)) {
            $this->info("✓ Overdue payment notification sent successfully");
        } else {
            $this->error("✗ Failed to send overdue payment notification");
        }
        
        $this->info("\nPayment notification testing completed!");
        $this->info("Check your email and logs for delivery status.");
    }
}
