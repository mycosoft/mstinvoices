<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report->name }} - Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .report-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }
        
        .summary-item h3 {
            margin: 0;
            color: #007bff;
            font-size: 18px;
        }
        
        .summary-item p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .section-title {
            background-color: #f8f9fa;
            padding: 10px;
            margin: 20px 0 10px 0;
            border-left: 4px solid #007bff;
            font-weight: bold;
            font-size: 14px;
        }
        
        .insights {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .insights h4 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
        
        .insights ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .insights li {
            margin: 5px 0;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $report->name }}</h1>
        <p>{{ ucfirst($report->type) }} Report - {{ ucfirst($report->format) }} Format</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
    
    <!-- Company and Report Info -->
    <div class="clearfix">
        <div class="company-info">
            @php
                $settings = \App\Models\Setting::forUser(auth()->id());
            @endphp
            @if($settings)
                <strong>{{ $settings->company_name ?? 'MST Invoices' }}</strong><br>
                @if($settings->company_address)
                    {{ $settings->company_address }}<br>
                @endif
                @if($settings->company_phone)
                    Phone: {{ $settings->company_phone }}<br>
                @endif
                @if($settings->company_email)
                    Email: {{ $settings->company_email }}
                @endif
            @else
                <strong>MST Invoices</strong><br>
                Professional Invoice Management System
            @endif
        </div>
        
        <div class="report-info">
            <strong>Report Details</strong><br>
            Status: <span class="badge badge-{{ $report->status === 'active' ? 'success' : ($report->status === 'archived' ? 'secondary' : 'warning') }}">{{ ucfirst($report->status) }}</span><br>
            Date Range: @if($report->date_range_type === 'custom')
                {{ $report->date_from->format('M d, Y') }} - {{ $report->date_to->format('M d, Y') }}
            @else
                {{ ucwords(str_replace('_', ' ', $report->date_range_type)) }}
            @endif<br>
            Generated: {{ $report->generation_count }} times<br>
            Last Generated: {{ $report->last_generated_at ? $report->last_generated_at->format('M d, Y') : 'Never' }}
        </div>
    </div>
    
    @if($report->description)
    <div class="insights">
        <h4>Report Description</h4>
        <p>{{ $report->description }}</p>
    </div>
    @endif
    
    <!-- Report Content Based on Type -->
    @if($report->type === 'revenue')
        @include('reports.pdf.revenue-report', ['data' => $reportData])
    @elseif($report->type === 'client')
        @include('reports.pdf.client-report', ['data' => $reportData])
    @elseif($report->type === 'monthly' || $report->type === 'yearly')
        @include('reports.pdf.time-based-report', ['data' => $reportData])
    @elseif($report->type === 'invoice_summary')
        @include('reports.pdf.invoice-summary-report', ['data' => $reportData])
    @elseif($report->type === 'payment_summary')
        @include('reports.pdf.payment-summary-report', ['data' => $reportData])
    @elseif($report->type === 'tax_summary')
        @include('reports.pdf.tax-summary-report', ['data' => $reportData])
    @else
        @include('reports.pdf.custom-report', ['data' => $reportData])
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by MST Invoices System</p>
        <p>Report ID: {{ $report->id }} | Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>