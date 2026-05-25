<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Setting;
use App\Services\SimpleEmailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentNotificationService
{
    protected $simpleEmailService;

    public function __construct(SimpleEmailService $simpleEmailService)
    {
        $this->simpleEmailService = $simpleEmailService;
    }

    /**
     * Send payment received notification
     */
    public function sendPaymentReceivedNotification(Invoice $invoice, InvoicePayment $payment)
    {
        try {
            return $this->simpleEmailService->sendPaymentReceived($invoice, $payment);
            
        } catch (\Exception $e) {
            Log::error('Failed to send payment notification', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    
    /**
     * Send payment reminder notification
     */
    public function sendPaymentReminderNotification(Invoice $invoice)
    {
        try {
            return $this->simpleEmailService->sendPaymentReminder($invoice);
        } catch (\Exception $e) {
            Log::error('Failed to send payment reminder', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send overdue payment notification
     */
    public function sendOverduePaymentNotification(Invoice $invoice)
    {
        try {
            return $this->simpleEmailService->sendOverduePayment($invoice);
        } catch (\Exception $e) {
            Log::error('Failed to send overdue payment notification', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
