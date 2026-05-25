<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quotation {{ $quotation->quotation_number }}</title>
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
        .quotation-title {
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
        .quotation-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .quotation-details {
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
            background: #e8f5e8;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #28a745;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #1e7e34;
            margin: 0;
        }
        .message-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
        }
        .validity-info {
            background: #fff3cd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .expired-info {
            background: #f8d7da;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
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
        .status-draft { background: #e2e3e5; color: #383d41; }
        .status-sent { background: #d1ecf1; color: #0c5460; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $settings->company_name ?? 'Invoice Generator' }}</div>
            <div class="quotation-title">📋 Quotation Notification</div>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hello <strong>{{ $quotation->client->display_name ?? 'Valued Customer' }}</strong>,</p>
                <p>Thank you for your interest! Please find our quotation for your requested services below.</p>
            </div>


            <div class="quotation-card">
                <h3 style="margin-top: 0; color: #28a745;">📋 Quotation Details</h3>
                
                <div class="quotation-details">
                    <div class="detail-item">
                        <span class="detail-label">Quotation #:</span>
                        <span class="detail-value">{{ $quotation->quotation_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ $quotation->quotation_date->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Valid Until:</span>
                        <span class="detail-value">{{ $quotation->valid_until->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-{{ $quotation->status }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="amount-highlight">
                <div class="total-amount">{{ $settings->formatCurrency($quotation->total_amount) }}</div>
            </div>

            @if($quotation->valid_until->isFuture())
            <div class="validity-info">
                <h4 style="margin-top: 0; color: #856404;">⏰ Quotation Validity</h4>
                <p><strong>This quotation is valid until {{ $quotation->valid_until->format('M d, Y') }}</strong></p>
                <p>Please review the quotation and let us know if you would like to proceed. Once accepted, we can convert this quotation into an invoice and begin work on your project.</p>
            </div>
            @else
            <div class="expired-info">
                <h4 style="margin-top: 0; color: #721c24;">⚠️ Quotation Expired</h4>
                <p>This quotation expired on {{ $quotation->valid_until->format('M d, Y') }}.</p>
                <p>Please contact us for an updated quotation if you're still interested in our services.</p>
            </div>
            @endif

            <p style="text-align: center; margin: 30px 0;">
                <strong>📎 Please find the detailed quotation attached as a PDF file.</strong>
            </p>
            
            <p style="text-align: center; color: #6c757d;">
                We look forward to working with you!
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
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
