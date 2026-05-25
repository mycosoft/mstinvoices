<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Received - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .payment-title {
            font-size: 18px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .payment-card {
            background: #e8f5e8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .invoice-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .summary-card {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .payment-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            font-weight: 500;
        }
        .amount-highlight {
            background: #d4edda;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #28a745;
        }
        .payment-amount {
            font-size: 28px;
            font-weight: bold;
            color: #1e7e34;
            margin: 0;
        }
        .balance-info {
            font-size: 16px;
            margin: 5px 0 0 0;
        }
        .balance-paid { color: #1e7e34; }
        .balance-remaining { color: #dc3545; }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-partial { background: #fff3cd; color: #856404; }
        .status-unpaid { background: #f8d7da; color: #721c24; }
        .success-message {
            background: #d4edda;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .warning-message {
            background: #fff3cd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $settings->company_name ?? 'Invoice Generator' }}</div>
            <div class="payment-title">💰 Payment Received</div>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hello <strong>{{ $invoice->client->display_name ?? 'Valued Customer' }}</strong>,</p>
                <p>Thank you for your payment! We have successfully received your payment for the invoice below.</p>
            </div>

            <div class="payment-card">
                <h3 style="margin-top: 0; color: #28a745;">💳 Payment Details</h3>
                
                <div class="payment-details">
                    <div class="detail-item">
                        <span class="detail-label">Payment Amount:</span>
                        <span class="detail-value">{{ $settings->formatCurrency($payment->amount) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Date:</span>
                        <span class="detail-value">{{ $payment->payment_date->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value">
                            @if($payment->payment_method == 'cash')
                                💵 Cash
                            @elseif($payment->payment_method == 'bank_transfer')
                                🏦 Bank Transfer
                            @elseif($payment->payment_method == 'mobile_money')
                                📱 Mobile Money
                            @elseif($payment->payment_method == 'cheque')
                                📄 Cheque
                            @else
                                {{ ucfirst($payment->payment_method) }}
                            @endif
                        </span>
                    </div>
                    @if($payment->notes)
                    <div class="detail-item">
                        <span class="detail-label">Notes:</span>
                        <span class="detail-value">{{ $payment->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="invoice-card">
                <h3 style="margin-top: 0; color: #007bff;">📄 Invoice Information</h3>
                
                <div class="payment-details">
                    <div class="detail-item">
                        <span class="detail-label">Invoice #:</span>
                        <span class="detail-value">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Invoice Date:</span>
                        <span class="detail-value">{{ $invoice->invoice_date->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Due Date:</span>
                        <span class="detail-value">{{ $invoice->due_date->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">{{ $settings->formatCurrency($invoice->total_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="amount-highlight">
                <div class="payment-amount">{{ $settings->formatCurrency($payment->amount) }}</div>
                <div class="balance-info">Payment Received</div>
            </div>

            <div class="summary-card">
                <h3 style="margin-top: 0; color: #1976d2;">📊 Payment Summary</h3>
                
                <div class="payment-details">
                    <div class="detail-item">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">{{ $settings->formatCurrency($invoice->total_amount) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Amount Paid:</span>
                        <span class="detail-value balance-paid">{{ $settings->formatCurrency($invoice->paid_amount) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Balance Due:</span>
                        <span class="detail-value balance-remaining">{{ $settings->formatCurrency($invoice->balance_due) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-{{ $invoice->payment_status }}">
                                @if($invoice->payment_status == 'paid')
                                    ✅ Fully Paid
                                @elseif($invoice->payment_status == 'partial')
                                    ⚠️ Partially Paid
                                @else
                                    ❌ Unpaid
                                @endif
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            @if($invoice->balance_due > 0)
            <div class="warning-message">
                <h4 style="margin-top: 0; color: #856404;">⚠️ Outstanding Balance</h4>
                <p>There is still an outstanding balance of <strong>{{ $settings->formatCurrency($invoice->balance_due) }}</strong> on this invoice.</p>
                <p>Please ensure the remaining amount is paid by the due date to avoid any late fees.</p>
            </div>
            @else
            <div class="success-message">
                <h4 style="margin-top: 0; color: #155724;">🎉 Invoice Fully Paid!</h4>
                <p>Thank you! This invoice has been fully paid. We appreciate your prompt payment.</p>
            </div>
            @endif

            <p style="text-align: center; margin: 30px 0; color: #6c757d;">
                <strong>📎 Please find your payment receipt attached as a PDF file.</strong>
            </p>
            
            <p style="text-align: center; margin: 30px 0; color: #6c757d;">
                If you have any questions about this payment, please don't hesitate to contact us.
            </p>
        </div>

        <div class="footer">
            <p><strong>{{ $settings->company_name ?? 'Invoice Generator' }}</strong></p>
            @if($settings->company_email)
                <p>📧 <a href="mailto:{{ $settings->company_email }}" style="color: #28a745;">{{ $settings->company_email }}</a></p>
            @endif
            @if($settings->company_phone)
                <p>📞 {{ $settings->company_phone }}</p>
            @endif
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This is an automated payment confirmation email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
