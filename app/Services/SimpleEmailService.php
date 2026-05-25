<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\InvoicePayment;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SimpleEmailService
{
    /**
     * Send invoice email
     */
    public function sendInvoice(Invoice $invoice, string $email, string $subject = null, string $message = null, bool $sendCopy = false)
    {
        try {
            $settings = Setting::forUser($invoice->user_id);
            
            // Use test email if in development
            $recipientEmail = $this->getTestEmail($email);
            
            $emailData = [
                'invoice' => $invoice,
                'settings' => $settings,
                'email_message' => $message,
            ];
            
            $subject = $subject ?: "Invoice {$invoice->invoice_number} - {$settings->company_name}";
            
            // Generate PDF
            $pdf = app('dompdf.wrapper')->loadView('invoices.pdf', $emailData);
            
            Mail::send('emails.simple-invoice', $emailData, function ($mail) use ($recipientEmail, $subject, $invoice, $pdf, $settings, $sendCopy) {
                $mail->to($recipientEmail)
                     ->subject($subject)
                     ->attachData($pdf->output(), "invoice-{$invoice->invoice_number}.pdf", [
                         'mime' => 'application/pdf',
                     ]);
                
                // Send copy to company if requested
                if ($sendCopy && $settings->company_email) {
                    $mail->cc($settings->company_email);
                }
                
                $mail->from($settings->company_email ?? config('mail.from.address'), $settings->company_name ?? config('mail.from.name'));
            });
            
            // Update invoice status
            if ($invoice->status === 'draft') {
                $invoice->markAsSent();
            }
            
            $invoice->update(['last_sent_at' => now()]);
            
            Log::info('Invoice email sent successfully', [
                'invoice_id' => $invoice->id,
                'recipient' => $recipientEmail,
                'test_mode' => $this->isTestMode()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send quotation email
     */
    public function sendQuotation(Quotation $quotation, string $email, string $subject = null, string $message = null, bool $sendCopy = false)
    {
        try {
            $settings = Setting::forUser($quotation->user_id);
            
            // Use test email if in development
            $recipientEmail = $this->getTestEmail($email);
            
            $emailData = [
                'quotation' => $quotation,
                'settings' => $settings,
                'email_message' => $message,
            ];
            
            $subject = $subject ?: "Quotation {$quotation->quotation_number} - {$settings->company_name}";
            
            // Generate PDF
            $pdf = app('dompdf.wrapper')->loadView('quotations.pdf', $emailData);
            
            Mail::send('emails.simple-quotation', $emailData, function ($mail) use ($recipientEmail, $subject, $quotation, $pdf, $settings, $sendCopy) {
                $mail->to($recipientEmail)
                     ->subject($subject)
                     ->attachData($pdf->output(), "quotation-{$quotation->quotation_number}.pdf", [
                         'mime' => 'application/pdf',
                     ]);
                
                // Send copy to company if requested
                if ($sendCopy && $settings->company_email) {
                    $mail->cc($settings->company_email);
                }
                
                $mail->from($settings->company_email ?? config('mail.from.address'), $settings->company_name ?? config('mail.from.name'));
            });
            
            // Update quotation status
            if ($quotation->status === 'draft') {
                $quotation->markAsSent();
            }
            
            $quotation->update(['last_sent_at' => now()]);
            
            Log::info('Quotation email sent successfully', [
                'quotation_id' => $quotation->id,
                'recipient' => $recipientEmail,
                'test_mode' => $this->isTestMode()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send quotation email', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send payment received notification
     */
    public function sendPaymentReceived(Invoice $invoice, InvoicePayment $payment)
    {
        try {
            $settings = Setting::forUser($invoice->user_id);
            
            // Use test email if in development
            $recipientEmail = $this->getTestEmail($invoice->client->email);
            
            $emailData = [
                'invoice' => $invoice,
                'payment' => $payment,
                'settings' => $settings,
            ];
            
            $subject = "Payment Received - Invoice {$invoice->invoice_number}";
            
            // Generate receipt PDF
            $receiptPdf = app('dompdf.wrapper')->loadView('receipts.pdf', $emailData);
            
            Mail::send('emails.simple-payment-received', $emailData, function ($mail) use ($recipientEmail, $subject, $invoice, $payment, $settings, $receiptPdf) {
                $mail->to($recipientEmail)
                     ->subject($subject)
                     ->attachData($receiptPdf->output(), "receipt-{$payment->id}.pdf", [
                         'mime' => 'application/pdf',
                     ])
                     ->from($settings->company_email ?? config('mail.from.address'), $settings->company_name ?? config('mail.from.name'));
            });
            
            Log::info('Payment received email sent successfully', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'recipient' => $recipientEmail,
                'test_mode' => $this->isTestMode()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send payment received email', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(Invoice $invoice)
    {
        try {
            $settings = Setting::forUser($invoice->user_id);
            
            // Use test email if in development
            $recipientEmail = $this->getTestEmail($invoice->client->email);
            
            $emailData = [
                'invoice' => $invoice,
                'settings' => $settings,
            ];
            
            $subject = "Payment Reminder - Invoice {$invoice->invoice_number}";
            
            Mail::send('emails.simple-payment-reminder', $emailData, function ($mail) use ($recipientEmail, $subject, $invoice, $settings) {
                $mail->to($recipientEmail)
                     ->subject($subject)
                     ->from($settings->company_email ?? config('mail.from.address'), $settings->company_name ?? config('mail.from.name'));
            });
            
            Log::info('Payment reminder sent successfully', [
                'invoice_id' => $invoice->id,
                'recipient' => $recipientEmail,
                'test_mode' => $this->isTestMode()
            ]);
            
            return true;
            
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
    public function sendOverduePayment(Invoice $invoice)
    {
        try {
            $settings = Setting::forUser($invoice->user_id);
            
            // Use test email if in development
            $recipientEmail = $this->getTestEmail($invoice->client->email);
            
            $emailData = [
                'invoice' => $invoice,
                'settings' => $settings,
            ];
            
            $subject = "URGENT: Overdue Payment - Invoice {$invoice->invoice_number}";
            
            Mail::send('emails.simple-overdue-payment', $emailData, function ($mail) use ($recipientEmail, $subject, $invoice, $settings) {
                $mail->to($recipientEmail)
                     ->subject($subject)
                     ->from($settings->company_email ?? config('mail.from.address'), $settings->company_name ?? config('mail.from.name'));
            });
            
            Log::info('Overdue payment notification sent successfully', [
                'invoice_id' => $invoice->id,
                'recipient' => $recipientEmail,
                'test_mode' => $this->isTestMode()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send overdue payment notification', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get test email address if in development mode
     */
    private function getTestEmail(string $originalEmail): string
    {
        if ($this->isTestMode()) {
            return 'mycosoftt@gmail.com';
        }
        return $originalEmail;
    }
    
    /**
     * Check if we're in test mode
     */
    private function isTestMode(): bool
    {
        return config('app.debug') || config('app.env') === 'local';
    }
}
