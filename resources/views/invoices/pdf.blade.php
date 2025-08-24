<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background: white;
            padding: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 0;
        }
        
        .header {
            background: #4472C4;
            color: white;
            padding: 20px 30px;
            display: table;
            width: 100%;
        }
        
        .company-name {
            display: table-cell;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            vertical-align: middle;
        }
        
        .invoice-title {
            display: table-cell;
            font-size: 24px;
            font-weight: bold;
            text-align: right;
            vertical-align: middle;
        }
        
        .content {
            padding: 30px;
        }
        
        .company-info {
            margin-bottom: 30px;
        }
        
        .company-info p {
            margin: 3px 0;
            font-size: 13px;
        }
        
        .main-info {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        
        .bill-to {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        
        .invoice-details {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            text-align: right;
        }
        
        .bill-to h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .bill-to p {
            margin: 3px 0;
            font-size: 13px;
        }
        
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .invoice-details td {
            padding: 5px 0;
            font-size: 13px;
        }
        
        .invoice-details .label {
            font-weight: bold;
            text-align: right;
            padding-right: 10px;
        }
        
        .invoice-details .value {
            text-align: right;
        }
        
        .invoice-number {
            font-weight: bold;
            color: #4472C4;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }
        
        .items-table td {
            border: 1px solid #ddd;
            padding: 10px 8px;
            font-size: 13px;
            vertical-align: top;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .item-description {
            color: #666;
            font-size: 12px;
            margin-top: 3px;
        }
        
        .totals {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals td {
            padding: 8px 0;
            font-size: 14px;
        }
        
        .totals .label {
            text-align: right;
            padding-right: 20px;
            font-weight: bold;
        }
        
        .totals .amount {
            text-align: right;
            width: 100px;
        }
        
        .total-row {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            padding: 12px 0;
        }
        
        .footer-section {
            margin-bottom: 20px;
        }
        
        .footer-section h4 {
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
        }
        
        .footer-section p {
            font-size: 12px;
            line-height: 1.4;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #28a745;
            color: white;
            border: 1px solid #1e7e34;
        }
        
        .status-pending {
            background: #ffc107;
            color: #212529;
            border: 1px solid #e0a800;
        }
        
        .status-overdue {
            background: #dc3545;
            color: white;
            border: 1px solid #bd2130;
        }
        
        .status-draft {
            background: #6c757d;
            color: white;
            border: 1px solid #545b62;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $settings->company_name ?? 'YOUR COMPANY NAME' }}</div>
            <div class="invoice-title">INVOICE</div>
        </div>
        
        <div class="content">
            <div class="company-info">
                @if($settings->company_address)
                    <p>{{ $settings->company_address }}</p>
                @endif
                @if($settings->company_city || $settings->company_state || $settings->company_postal_code)
                    <p>{{ implode(', ', array_filter([$settings->company_city, $settings->company_state, $settings->company_postal_code])) }}</p>
                @endif
                @if($settings->company_phone)
                    <p>{{ $settings->company_phone }}</p>
                @endif
                @if($settings->company_email)
                    <p>{{ $settings->company_email }}</p>
                @endif
            </div>
            
            <div class="main-info">
                <div class="bill-to">
                    <h3>Bill To</h3>
                    <p><strong>{{ $invoice->client->display_name }}</strong></p>
                    @if($invoice->client->company_name && $invoice->client->company_name !== $invoice->client->name)
                        <p>{{ $invoice->client->company_name }}</p>
                    @endif
                    @if($invoice->client->full_address)
                        <p>{{ $invoice->client->full_address }}</p>
                    @endif
                    @if($invoice->client->email)
                        <p>{{ $invoice->client->email }}</p>
                    @endif
                </div>
                
                <div class="invoice-details">
                    <table>
                        <tr>
                            <td class="label">Invoice #</td>
                            <td class="value invoice-number">{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="label">Invoice Date</td>
                            <td class="value">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Due Date</td>
                            <td class="value">{{ $invoice->due_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value">
                                <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->invoiceItems as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->item_description)
                                <div class="item-description">{{ $item->item_description }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ $settings->formatCurrency($item->unit_price) }}</td>
                        <td class="text-right">{{ $settings->formatCurrency($item->total_amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="totals">
                <table>
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">{{ $settings->formatCurrency($invoice->subtotal) }}</td>
                    </tr>
                    @if($invoice->tax_amount > 0)
                    <tr>
                        <td class="label">Tax:</td>
                        <td class="amount">{{ $settings->formatCurrency($invoice->tax_amount) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label">TOTAL:</td>
                        <td class="amount">{{ $settings->formatCurrency($invoice->total_amount) }}</td>
                    </tr>
                </table>
            </div>
            
            @if($invoice->notes)
            <div class="footer-section">
                <h4>Notes</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif
            
            <div class="footer-section">
                <h4>Payment Information</h4>
                <p><strong>Payment is due by {{ $invoice->due_date->format('M d, Y') }}</strong></p>
                <br>
                <p><strong>Bank Account Details:</strong></p>
                <p>Bank: Equity Bank</p>
                <p>Account Name: SSENJOBE MICHEAL</p>
                <p>Account Number: 1043100796103</p>
                <p>Swift Code: EQBLUGKA</p>
                <br>
                <p><strong>Mobile Money:</strong></p>
                <p>Name: SSENJOBE MICHEAL</p>
                <p>Phone: +256 750501151 / 0781779477</p>
                <br>
                <p>For questions regarding this invoice, please contact {{ $settings->company_email ?? 'mycosoftofficial@gmail.com' }}.</p>
            </div>
        </div>
    </div>
</body>
</html>