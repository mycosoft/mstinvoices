<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class RecalculateInvoicePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:recalculate-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all invoice payment statuses based on actual payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating invoice payment statuses...');
        
        $invoices = Invoice::with(['payments'])->get();
        $recalculatedCount = 0;
        
        foreach ($invoices as $invoice) {
            $originalStatus = $invoice->payment_status;
            $originalBalance = $invoice->balance_due;
            $originalPaid = $invoice->paid_amount;
            
            $invoice->recalculatePaymentStatus();
            
            $recalculatedCount++;
            
            $this->line("Recalculated Invoice {$invoice->invoice_number}:");
            $this->line("  Status: {$originalStatus} → {$invoice->payment_status}");
            $this->line("  Balance: {$originalBalance} → {$invoice->balance_due}");
            $this->line("  Paid: {$originalPaid} → {$invoice->paid_amount}");
            $this->line("  Payments: {$invoice->payments->count()}");
            $this->line("");
        }
        
        $this->info("Recalculated {$recalculatedCount} invoices.");
        
        // Show summary of all invoices
        $this->info("\nCurrent invoice status:");
        foreach ($invoices as $invoice) {
            $this->line("Invoice {$invoice->invoice_number}: Status={$invoice->status}, Payment={$invoice->payment_status}, Total={$invoice->total_amount}, Paid={$invoice->paid_amount}, Balance={$invoice->balance_due}, Payments={$invoice->payments->count()}");
        }
    }
}
