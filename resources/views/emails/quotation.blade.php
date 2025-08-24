<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quotation {{ $quotation->quotation_number }}</title>
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
        .quotation-title {
            font-size: 18px;
            color: #666;
        }
        .content {
            margin-bottom: 30px;
        }
        .quotation-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .quotation-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .quotation-details td {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .quotation-details .label {
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
        .validity-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .validity-info h4 {
            margin-top: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ isset($settings) && $settings->company_name ? $settings->company_name : 'Invoice Generator' }}</div>
            <div class="quotation-title">Quotation Notification</div>
        </div>

        <div class="content">
            <p>Dear {{ isset($quotation) && isset($quotation->client) && $quotation->client->display_name ? $quotation->client->display_name : 'Valued Customer' }},</p>
            
            <p>We are pleased to provide you with our quotation for the services you requested.</p>
            
            @if(isset($email_message) && $email_message)
            <div class="message">
                <strong>Message from {{ isset($settings) && $settings->company_name ? $settings->company_name : 'us' }}:</strong><br>
                {!! nl2br(e($email_message)) !!}
            </div>
            @endif

            @if(isset($quotation))
            <div class="quotation-details">
                <table>
                    <tr>
                        <td class="label">Quotation Number:</td>
                        <td><strong>{{ $quotation->quotation_number ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Quotation Date:</td>
                        <td>{{ isset($quotation->quotation_date) && $quotation->quotation_date ? $quotation->quotation_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Valid Until:</td>
                        <td>{{ isset($quotation->valid_until) && $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status:</td>
                        <td><span style="color: {{ isset($quotation->status) && $quotation->status === 'accepted' ? '#28a745' : '#dc3545' }};">{{ isset($quotation->status) ? ucfirst($quotation->status) : 'Unknown' }}</span></td>
                    </tr>
                </table>
            </div>

            <div class="amount">
                Total Amount: {{ isset($quotation) && isset($settings) ? $settings->formatCurrency($quotation->total_amount) : 'N/A' }}
            </div>

            @if(isset($quotation->valid_until) && $quotation->valid_until->isFuture())
            <div class="validity-info">
                <h4>Important: Quotation Validity</h4>
                <p><strong>This quotation is valid until {{ $quotation->valid_until->format('M d, Y') }}</strong></p>
                <p>Please review the quotation and let us know if you would like to proceed. Once accepted, we can convert this quotation into an invoice and begin work on your project.</p>
            </div>
            @elseif(isset($quotation->valid_until))
            <div class="validity-info">
                <h4>Quotation Expired</h4>
                <p>This quotation expired on {{ $quotation->valid_until->format('M d, Y') }}. Please contact us for an updated quotation.</p>
            </div>
            @endif
            @endif

            <p>Please find the detailed quotation attached as a PDF file. If you have any questions regarding this quotation, please don't hesitate to contact us.</p>
            
            <p>We look forward to working with you!</p>
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