<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
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
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary h3 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            padding: 5px 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-cell:first-child {
            font-weight: bold;
            width: 60%;
        }
        
        .summary-cell:last-child {
            text-align: right;
            color: #28a745;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
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
        
        .text-success {
            color: #28a745;
        }
        
        .text-warning {
            color: #ffc107;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($type) }} Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
        @if(isset($reportData['date_range']))
            <p>Period: {{ $reportData['date_range'][0]->format('M d, Y') }} to {{ $reportData['date_range'][1]->format('M d, Y') }}</p>
        @endif
    </div>

    @if($type == 'revenue')
        <div class="summary">
            <h3>Revenue Summary</h3>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Total Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['total_revenue'], 0) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Paid Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['paid_revenue'], 0) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Pending Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['pending_revenue'], 0) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Invoice Count:</div>
                    <div class="summary-cell">{{ number_format($reportData['summary']['invoice_count']) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Average Invoice Value:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['average_invoice_value'], 0) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Payment Rate:</div>
                    <div class="summary-cell">{{ number_format($reportData['summary']['payment_rate'], 1) }}%</div>
                </div>
            </div>
        </div>

        @if(isset($reportData['monthly_data']) && count($reportData['monthly_data']) > 0)
        <h3>Monthly Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th class="text-center">Invoices</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Paid Revenue</th>
                    <th class="text-center">Payment Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['monthly_data'] as $data)
                <tr>
                    <td>{{ DateTime::createFromFormat('!m', $data->month)->format('F') }}</td>
                    <td>{{ $data->year }}</td>
                    <td class="text-center">{{ $data->invoice_count }}</td>
                    <td class="text-right">UGX {{ number_format($data->total_revenue, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($data->paid_revenue, 0) }}</td>
                    <td class="text-center">{{ number_format(($data->paid_revenue / max(1, $data->total_revenue)) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    @elseif($type == 'client')
        <div class="summary">
            <h3>Client Performance Summary</h3>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Total Clients:</div>
                    <div class="summary-cell">{{ number_format($reportData['summary']['total_clients']) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Active Clients:</div>
                    <div class="summary-cell">{{ number_format($reportData['summary']['active_clients']) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Total Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['total_revenue'], 0) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Average Revenue per Client:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['average_revenue_per_client'], 0) }}</div>
                </div>
            </div>
        </div>

        @if(isset($reportData['client_data']) && count($reportData['client_data']) > 0)
        <h3>Client Performance Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Company</th>
                    <th class="text-center">Invoices</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Paid Revenue</th>
                    <th class="text-right">Outstanding</th>
                    <th class="text-center">Payment Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['client_data'] as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->company_name ?: 'Individual' }}</td>
                    <td class="text-center">{{ $client->invoice_count }}</td>
                    <td class="text-right">UGX {{ number_format($client->total_revenue, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($client->paid_revenue, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($client->outstanding_amount, 0) }}</td>
                    <td class="text-center">{{ number_format(($client->paid_revenue / max(1, $client->total_revenue)) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    @elseif($type == 'monthly')
        <div class="summary">
            <h3>Monthly Summary for {{ $reportData['summary']['year'] ?? 'N/A' }}</h3>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Total Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['total_revenue'], 0) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Total Invoices:</div>
                    <div class="summary-cell">{{ number_format($reportData['summary']['total_invoices']) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Average Monthly Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['average_monthly_revenue'], 0) }}</div>
                </div>
            </div>
        </div>

        @if(isset($reportData['monthly_data']) && count($reportData['monthly_data']) > 0)
        <h3>Monthly Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-center">Invoices</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Paid Revenue</th>
                    <th class="text-right">Average Invoice</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['monthly_data'] as $data)
                <tr>
                    <td>{{ DateTime::createFromFormat('!m', $data->month)->format('F') }}</td>
                    <td class="text-center">{{ $data->invoice_count }}</td>
                    <td class="text-right">UGX {{ number_format($data->total_revenue, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($data->paid_revenue, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($data->average_invoice_value, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    @elseif($type == 'yearly')
        <div class="summary">
            <h3>Yearly Summary ({{ $reportData['summary']['years_range'] ?? 'N/A' }})</h3>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Total Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['total_revenue'], 0) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Total Invoices:</div>
                    <div class="summary-cell">{{ number_format($reportData['summary']['total_invoices']) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Average Yearly Revenue:</div>
                    <div class="summary-cell">UGX {{ number_format($reportData['summary']['average_yearly_revenue'], 0) }}</div>
                </div>
            </div>
        </div>

        @if(isset($reportData['yearly_data']) && count($reportData['yearly_data']) > 0)
        <h3>Yearly Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Year</th>
                    <th class="text-center">Invoices</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Paid Revenue</th>
                    <th class="text-right">Average Invoice</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['yearly_data'] as $data)
                <tr>
                    <td>{{ $data->year }}</td>
                    <td class="text-center">{{ $data->invoice_count }}</td>
                    <td class="text-right">UGX {{ number_format($data->total_revenue, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($data->paid_revenue, 0) }}</td>
                    <td class="text-right">UGX {{ number_format($data->average_invoice_value, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Invoice Generator System</p>
        <p>{{ config('app.name', 'Invoice Generator') }} - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>