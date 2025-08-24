<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Report: {{ $report->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            margin: -30px -30px 20px -30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .report-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        
        .report-info h3 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        .summary-section {
            margin: 20px 0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        
        .summary-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .summary-card h4 {
            margin: 0 0 5px 0;
            color: #007bff;
            font-size: 18px;
        }
        
        .summary-card p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .attachment-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #bee5eb;
        }
        
        .attachment-info h4 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Scheduled Report Generated</h1>
            <p>Your automated report is ready for review</p>
        </div>
        
        <p>Hello,</p>
        
        <p>Your scheduled report "<strong>{{ $report->name }}</strong>" has been automatically generated and is attached to this email.</p>
        
        <div class="report-info">
            <h3>📋 Report Details</h3>
            <div class="info-row">
                <span class="info-label">Report Name:</span>
                <span class="info-value">{{ $report->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Report Type:</span>
                <span class="info-value">{{ ucfirst(str_replace('_', ' ', $report->type)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated On:</span>
                <span class="info-value">{{ now()->format('F j, Y \a\t g:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Schedule:</span>
                <span class="info-value">{{ ucfirst($report->schedule_frequency) }} at {{ $report->schedule_time }}</span>
            </div>
            @if($report->date_range_type === 'custom')
            <div class="info-row">
                <span class="info-label">Date Range:</span>
                <span class="info-value">{{ $report->date_from->format('M d, Y') }} - {{ $report->date_to->format('M d, Y') }}</span>
            </div>
            @else
            <div class="info-row">
                <span class="info-label">Period:</span>
                <span class="info-value">{{ ucwords(str_replace('_', ' ', $report->date_range_type)) }}</span>
            </div>
            @endif
        </div>
        
        @if(isset($reportData) && is_array($reportData) && isset($reportData['summary']))
        <div class="summary-section">
            <h3>📈 Report Summary</h3>
            <div class="summary-grid">
                @if($report->type === 'revenue')
                    <div class="summary-card">
                        <h4>UGX {{ number_format($reportData['summary']['total_revenue'] ?? 0, 0) }}</h4>
                        <p>Total Revenue</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['invoice_count'] ?? 0) }}</h4>
                        <p>Total Invoices</p>
                    </div>
                    <div class="summary-card">
                        <h4>UGX {{ number_format($reportData['summary']['average_invoice'] ?? 0, 0) }}</h4>
                        <p>Average Invoice</p>
                    </div>
                @elseif($report->type === 'client')
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['total_clients'] ?? 0) }}</h4>
                        <p>Total Clients</p>
                    </div>
                    <div class="summary-card">
                        <h4>UGX {{ number_format($reportData['summary']['total_revenue'] ?? 0, 0) }}</h4>
                        <p>Total Revenue</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['active_clients'] ?? 0) }}</h4>
                        <p>Active Clients</p>
                    </div>
                @elseif(in_array($report->type, ['monthly', 'yearly']))
                    <div class="summary-card">
                        <h4>UGX {{ number_format($reportData['summary']['total_revenue'] ?? 0, 0) }}</h4>
                        <p>Total Revenue</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['growth_rate'] ?? 0, 1) }}%</h4>
                        <p>Growth Rate</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['periods_count'] ?? 0) }}</h4>
                        <p>Periods Analyzed</p>
                    </div>
                @elseif($report->type === 'invoice_summary')
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['total_invoices'] ?? 0) }}</h4>
                        <p>Total Invoices</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['paid_invoices'] ?? 0) }}</h4>
                        <p>Paid Invoices</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['payment_rate'] ?? 0, 1) }}%</h4>
                        <p>Payment Rate</p>
                    </div>
                @elseif($report->type === 'payment_summary')
                    <div class="summary-card">
                        <h4>UGX {{ number_format($reportData['summary']['total_payments'] ?? 0, 0) }}</h4>
                        <p>Total Payments</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['payment_count'] ?? 0) }}</h4>
                        <p>Payment Count</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['ontime_payment_rate'] ?? 0, 1) }}%</h4>
                        <p>On-Time Rate</p>
                    </div>
                @elseif($report->type === 'tax_summary')
                    <div class="summary-card">
                        <h4>UGX {{ number_format($reportData['summary']['total_tax_collected'] ?? 0, 0) }}</h4>
                        <p>Tax Collected</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['effective_tax_rate'] ?? 0, 2) }}%</h4>
                        <p>Effective Tax Rate</p>
                    </div>
                    <div class="summary-card">
                        <h4>{{ number_format($reportData['summary']['tax_payment_rate'] ?? 0, 1) }}%</h4>
                        <p>Collection Rate</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
        
        <div class="attachment-info">
            <h4>📎 Report Attachment</h4>
            <p>The complete report has been attached as a PDF file to this email. You can open it to view detailed analysis, charts, and insights.</p>
            <p><strong>Filename:</strong> report-{{ \Str::slug($report->name) }}-{{ now()->format('Y-m-d') }}.pdf</p>
        </div>
        
        @if($report->description)
        <div class="report-info">
            <h3>📝 Report Description</h3>
            <p>{{ $report->description }}</p>
        </div>
        @endif
        
        <p>If you have any questions about this report or need assistance, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        MST Invoices Team</p>
        
        <div class="footer">
            <p>This is an automated email from MST Invoices. Please do not reply to this email.</p>
            <p>Generated on {{ now()->format('Y-m-d H:i:s') }} | Report ID: {{ $report->id }}</p>
            @php
                $settings = \App\Models\Setting::forUser($report->user_id);
            @endphp
            @if($settings && $settings->company_name)
                <p>{{ $settings->company_name }}</p>
            @endif
        </div>
    </div>
</body>
</html>