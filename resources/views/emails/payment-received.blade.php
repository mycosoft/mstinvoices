<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Received - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .payment-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .invoice-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .balance-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn-success {
            background: #28a745;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .balance {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💰 Payment Received</h1>
        <p>Thank you for your payment!</p>
    </div>
    
    <div class="content">
        <h2>Payment Details</h2>
        
        <div class="payment-details">
            <h3>Payment Information</h3>
            <table>
                <tr>
                    <td><strong>Payment Amount:</strong></td>
                    <td class="amount">{{ $settings->formatCurrency($payment->amount) }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Date:</strong></td>
                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Method:</strong></td>
                    <td>
                        @if($payment->payment_method == 'cash')
                            💵 Cash Payment
                        @elseif($payment->payment_method == 'bank_transfer')
                            🏦 Bank Transfer (Equity Bank)
                        @elseif($payment->payment_method == 'mobile_money')
                            📱 Mobile Money (Airtel / MTN)
                        @elseif($payment->payment_method == 'cheque')
                            📄 Cheque Payment
                        @endif
                    </td>
                </tr>
                @if($payment->notes)
                <tr>
                    <td><strong>Notes:</strong></td>
                    <td>{{ $payment->notes }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <div class="invoice-details">
            <h3>Invoice Information</h3>
            <table>
                <tr>
                    <td><strong>Invoice Number:</strong></td>
                    <td>{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td><strong>Invoice Date:</strong></td>
                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Due Date:</strong></td>
                    <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Client:</strong></td>
                    <td>{{ $invoice->client->display_name }}</td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td>{{ $settings->formatCurrency($invoice->total_amount) }}</td>
                </tr>
            </table>
        </div>
        
        <div class="balance-info">
            <h3>Payment Summary</h3>
            <table>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td>{{ $settings->formatCurrency($invoice->total_amount) }}</td>
                </tr>
                <tr>
                    <td><strong>Amount Paid:</strong></td>
                    <td class="amount">{{ $settings->formatCurrency($invoice->paid_amount) }}</td>
                </tr>
                <tr>
                    <td><strong>Balance Due:</strong></td>
                    <td class="balance">{{ $settings->formatCurrency($invoice->balance_due) }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Status:</strong></td>
                    <td>
                        @if($invoice->payment_status == 'paid')
                            <span style="color: #28a745; font-weight: bold;">✅ Fully Paid</span>
                        @elseif($invoice->payment_status == 'partial')
                            <span style="color: #ffc107; font-weight: bold;">⚠️ Partially Paid</span>
                        @else
                            <span style="color: #dc3545; font-weight: bold;">❌ Unpaid</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        @if($invoice->balance_due > 0)
        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <h4>⚠️ Outstanding Balance</h4>
            <p>There is still an outstanding balance of <strong>{{ $settings->formatCurrency($invoice->balance_due) }}</strong> on this invoice.</p>
            <p>Please ensure the remaining amount is paid by the due date to avoid any late fees.</p>
        </div>
        @else
        <div style="background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;">
            <h4>🎉 Invoice Fully Paid!</h4>
            <p>Thank you! This invoice has been fully paid. We appreciate your prompt payment.</p>
        </div>
        @endif
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn">View Invoice Details</a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-success">Download PDF</a>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated payment confirmation email from {{ $settings->company_name ?? 'Invoice Generator' }}.</p>
        <p>If you have any questions about this payment, please contact us at {{ $settings->company_email ?? 'support@example.com' }}.</p>
        <p>© {{ date('Y') }} {{ $settings->company_name ?? 'Invoice Generator' }}. All rights reserved.</p>
    </div>
</body>
</html>
