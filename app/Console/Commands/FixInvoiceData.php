<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class FixInvoiceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:fix-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice payment status and balance data inconsistencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing invoice data inconsistencies...');
        
        $invoices = Invoice::all();
        $fixedCount = 0;
        
        foreach ($invoices as $invoice) {
            $needsFix = false;
            $originalStatus = $invoice->payment_status;
            $originalBalance = $invoice->balance_due;
            $originalPaid = $invoice->paid_amount;
            
            // Check if invoice has no payments but is marked as paid
            if ($invoice->payments->count() == 0 && $invoice->payment_status == 'paid') {
                $needsFix = true;
                $invoice->payment_status = 'unpaid';
                $invoice->balance_due = $invoice->total_amount;
                $invoice->paid_amount = 0.00;
                $invoice->status = 'pending';
                $invoice->paid_date = null;
            }
            
            // Check if balance_due is 0 but no payments were made
            elseif ($invoice->balance_due == 0 && $invoice->paid_amount == 0 && $invoice->total_amount > 0) {
                $needsFix = true;
                $invoice->balance_due = $invoice->total_amount;
                $invoice->payment_status = 'unpaid';
                $invoice->status = 'pending';
            }
            
            // Check if paid_amount is 0 but payment_status is not unpaid
            elseif ($invoice->paid_amount == 0 && $invoice->payment_status != 'unpaid') {
                $needsFix = true;
                $invoice->payment_status = 'unpaid';
                $invoice->balance_due = $invoice->total_amount;
                $invoice->status = 'pending';
                $invoice->paid_date = null;
            }
            
            if ($needsFix) {
                $invoice->recalculatePaymentStatus();
                $fixedCount++;
                
                $this->line("Fixed Invoice {$invoice->invoice_number}:");
                $this->line("  Status: {$originalStatus} → {$invoice->payment_status}");
                $this->line("  Balance: {$originalBalance} → {$invoice->balance_due}");
                $this->line("  Paid: {$originalPaid} → {$invoice->paid_amount}");
                $this->line("  Payments: {$invoice->payments->count()}");
                $this->line("");
            }
        }
        
        $this->info("Fixed {$fixedCount} invoices with data inconsistencies.");
        
        // Show summary of all invoices
        $this->info("\nCurrent invoice status:");
        $invoices = Invoice::with(['payments'])->get();
        foreach ($invoices as $invoice) {
            $this->line("Invoice {$invoice->invoice_number}: Status={$invoice->status}, Payment={$invoice->payment_status}, Total={$invoice->total_amount}, Paid={$invoice->paid_amount}, Balance={$invoice->balance_due}, Payments={$invoice->payments->count()}");
        }
    }
}
