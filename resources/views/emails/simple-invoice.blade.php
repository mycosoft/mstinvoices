<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }}</title>
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
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-title {
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
        .invoice-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .invoice-details {
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
            background: #e3f2fd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #2196f3;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #1976d2;
            margin: 0;
        }
        .balance-due {
            font-size: 18px;
            color: #d32f2f;
            margin: 5px 0 0 0;
        }
        .message-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
        }
        .payment-info {
            background: #e8f5e8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-method {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 3px solid #4caf50;
        }
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
        .status-pending { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $settings->company_name ?? 'Invoice Generator' }}</div>
            <div class="invoice-title">📄 Invoice Notification</div>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hello <strong>{{ $invoice->client->display_name ?? 'Valued Customer' }}</strong>,</p>
                <p>Thank you for your business! Please find your invoice details below.</p>
            </div>

            @if(isset($email_message) && $email_message)
            <div class="message-box">
                <strong>📝 Message from {{ $settings->company_name ?? 'us' }}:</strong><br>
                {{ $email_message }}
            </div>
            @endif

            <div class="invoice-card">
                <h3 style="margin-top: 0; color: #007bff;">📋 Invoice Details</h3>
                
                <div class="invoice-details">
                    <div class="detail-item">
                        <span class="detail-label">Invoice #:</span>
                        <span class="detail-value">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ $invoice->invoice_date->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Due Date:</span>
                        <span class="detail-value">{{ $invoice->due_date->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-{{ $invoice->status }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="amount-highlight">
                <div class="total-amount">{{ $settings->formatCurrency($invoice->total_amount) }}</div>
                @if($invoice->balance_due > 0)
                    <div class="balance-due">Balance Due: {{ $settings->formatCurrency($invoice->balance_due) }}</div>
                @endif
            </div>

            @if($invoice->balance_due > 0)
            <div class="payment-info">
                <h4 style="margin-top: 0; color: #2e7d32;">💳 Payment Options</h4>
                
                <div class="payment-method">
                    <strong>🏦 Bank Transfer</strong><br>
                    <small>Equity Bank | Account: 1043100796103 | Name: SSENJOBE MICHEAL</small>
                </div>
                
                <div class="payment-method">
                    <strong>📱 Mobile Money</strong><br>
                    <small>SSENJOBE MICHEAL | +256 750501151 / 0781779477</small>
                </div>
                
                <div class="payment-method">
                    <strong>💵 Cash Payment</strong><br>
                    <small>Visit our office during business hours</small>
                </div>
            </div>
            @endif

            <p style="text-align: center; margin: 30px 0;">
                <strong>📎 Please find the detailed invoice attached as a PDF file.</strong>
            </p>
            
            <p style="text-align: center; color: #6c757d;">
                If you have any questions, please don't hesitate to contact us.
            </p>
        </div>

        <div class="footer">
            <p><strong>{{ $settings->company_name ?? 'Invoice Generator' }}</strong></p>
            @if($settings->company_email)
                <p>📧 <a href="mailto:{{ $settings->company_email }}" style="color: #007bff;">{{ $settings->company_email }}</a></p>
            @endif
            @if($settings->company_phone)
                <p>📞 {{ $settings->company_phone }}</p>
            @endif
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
