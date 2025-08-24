<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 18px;
            color: #666;
        }
        .content {
            margin-bottom: 30px;
        }
        .invoice-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .invoice-details .label {
            font-weight: bold;
            width: 40%;
        }
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .message {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #666;
        }
        .payment-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .payment-info h4 {
            margin-top: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ isset($settings) && $settings->company_name ? $settings->company_name : 'Invoice Generator' }}</div>
            <div class="invoice-title">Invoice Notification</div>
        </div>

        <div class="content">
            <p>Dear {{ isset($invoice) && isset($invoice->client) && $invoice->client->display_name ? $invoice->client->display_name : 'Valued Customer' }},</p>
            
            <p>We hope this email finds you well. Please find attached your invoice for services rendered.</p>
            
            @if(isset($email_message) && $email_message)
            <div class="message">
                <strong>Message from {{ isset($settings) && $settings->company_name ? $settings->company_name : 'us' }}:</strong><br>
                {!! nl2br(e($email_message)) !!}
            </div>
            @endif

            @if(isset($invoice))
            <div class="invoice-details">
                <table>
                    <tr>
                        <td class="label">Invoice Number:</td>
                        <td><strong>{{ $invoice->invoice_number ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Invoice Date:</td>
                        <td>{{ isset($invoice->invoice_date) && $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Due Date:</td>
                        <td>{{ isset($invoice->due_date) && $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status:</td>
                        <td><span style="color: {{ isset($invoice->status) && $invoice->status === 'paid' ? '#28a745' : '#dc3545' }};">{{ isset($invoice->status) ? ucfirst($invoice->status) : 'Unknown' }}</span></td>
                    </tr>
                </table>
            </div>

            <div class="amount">
                @if(isset($invoice->total_amount))
                Total Amount: {{ isset($settings) && method_exists($settings, 'formatCurrency') ? $settings->formatCurrency($invoice->total_amount) : '$' . number_format($invoice->total_amount, 2) }}
                @if(isset($invoice->balance_due) && $invoice->balance_due > 0)
                    <br><small style="color: #dc3545;">Amount Due: {{ isset($settings) && method_exists($settings, 'formatCurrency') ? $settings->formatCurrency($invoice->balance_due) : '$' . number_format($invoice->balance_due, 2) }}</small>
                @endif
                @endif
            </div>

            @if(isset($invoice->balance_due) && $invoice->balance_due > 0)
            <div class="payment-info">
                <h4>Payment Information</h4>
                <p><strong>Payment is due by {{ isset($invoice->due_date) && $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</strong></p>
                
                <p><strong>1. Cash Payment:</strong><br>
                Visit our office during business hours.</p>
                
                <p><strong>2. Bank Transfer:</strong><br>
                Bank: Equity Bank<br>
                Account Name: SSENJOBE MICHEAL<br>
                Account Number: 1043100796103<br>
                Swift Code: EQBLUGKA</p>
                
                <p><strong>3. Mobile Money:</strong><br>
                Name: SSENJOBE MICHEAL<br>
                Phone: +256 750501151 / 0781779477</p>
            </div>
            @endif
            @endif

            <p>Please find the detailed invoice attached as a PDF file. If you have any questions regarding this invoice, please don't hesitate to contact us.</p>
            
            <p>Thank you for your business!</p>
        </div>

        <div class="footer">
            @if(isset($settings) && $settings->company_name)
                <p><strong>{{ $settings->company_name }}</strong></p>
            @endif
            @if(isset($settings) && $settings->company_email)
                <p>Email: <a href="mailto:{{ $settings->company_email }}">{{ $settings->company_email }}</a></p>
            @endif
            @if(isset($settings) && $settings->company_phone)
                <p>Phone: {{ $settings->company_phone }}</p>
            @endif
            @if(isset($settings) && $settings->company_website)
                <p>Website: <a href="{{ $settings->company_website }}">{{ $settings->company_website }}</a></p>
            @endif
            
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>