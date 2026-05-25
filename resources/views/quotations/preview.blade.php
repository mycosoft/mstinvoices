<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quotation_number }} - Preview</title>
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
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            background: #4472C4;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .quotation-title {
            font-size: 24px;
            font-weight: bold;
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
        
        .company-logo {
            position: absolute;
            top: 20px;
            right: 30px;
            max-width: 150px;
            max-height: 80px;
        }
        
        .company-logo img {
            max-width: 100%;
            max-height: 100%;
            height: auto;
            width: auto;
        }
        
        .status-stamp {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }
        
        .content {
            position: relative;
        }
        
        .main-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        
        .bill-to {
            flex: 1;
        }
        
        .quotation-details {
            flex: 0 0 200px;
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
        
        .quotation-details table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .quotation-details td {
            padding: 5px 0;
            font-size: 13px;
        }
        
        .quotation-details .label {
            font-weight: bold;
            text-align: right;
            padding-right: 10px;
        }
        
        .quotation-details .value {
            text-align: right;
        }
        
        .quotation-number {
            font-weight: bold;
            color: #4472C4;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 2px solid #ddd;
        }
        
        .items-table th {
            background: #4472C4;
            color: white;
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
            float: right;
            width: 300px;
            margin-bottom: 30px;
            background: transparent;
            padding: 0;
        }
        
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals td {
            padding: 8px 0;
            font-size: 14px;
            border: none;
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
            background: #4472C4;
            color: white;
        }
        
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            padding: 12px 15px;
            color: white;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
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
        
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .print-controls button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }
        
        .btn-print {
            background: #4472C4;
            color: white;
        }
        
        .btn-close {
            background: #dc3545;
            color: white;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 3px solid;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .status-accepted {
            color: #28a745;
            border-color: #28a745;
        }
        
        .status-sent {
            color: #17a2b8;
            border-color: #17a2b8;
        }
        
        .status-viewed {
            color: #007bff;
            border-color: #007bff;
        }
        
        .status-rejected {
            color: #dc3545;
            border-color: #dc3545;
        }
        
        .status-expired {
            color: #ffc107;
            border-color: #ffc107;
        }
        
        .status-draft {
            color: #6c757d;
            border-color: #6c757d;
        }
        
        .status-converted {
            color: #343a40;
            border-color: #343a40;
        }
        
        @media (max-width: 768px) {
            .main-info {
                flex-direction: column;
                gap: 20px;
            }
            
            .quotation-details {
                text-align: left;
            }
            
            .totals {
                float: none;
                width: 100%;
            }
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                margin: 0;
            }
            
            .print-controls {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="btn-print" onclick="window.print()">🖨️ Print</button>
        <button class="btn-close" onclick="window.close()">✕ Close</button>
    </div>

    <div class="container">
        <div class="header">
            <div class="company-name">{{ $settings->company_name ?? 'YOUR COMPANY NAME' }}</div>
            <div class="quotation-title">QUOTATION</div>
        </div>
        
        <div class="content">
            @if($settings->company_logo_path)
                <div class="company-logo">
                    <img src="{{ asset('storage/' . $settings->company_logo_path) }}" alt="{{ $settings->company_name ?? 'Company Logo' }}">
                </div>
            @endif
            
            <div class="status-stamp">
                <span class="status-badge status-{{ $quotation->status }}">{{ ucfirst($quotation->status) }}</span>
            </div>
            
            <div class="company-info">
                @if($settings->company_address)
                    <p>{{ $settings->company_address }}</p>
                @endif
                @if($settings->company_city || $settings->company_state || $settings->company_postal_code)
                    <p>{{ implode(', ', array_filter([$settings->company_city, $settings->company_state, $settings->company_postal_code])) }}</p>
                @endif
                @if($settings->company_phone)
                    <p>Tel: {{ $settings->company_phone }}</p>
                @endif
                @if($settings->company_email)
                    <p>Email: {{ $settings->company_email }}</p>
                @endif
                @if($settings->company_website)
                    <p>Website: {{ $settings->company_website }}</p>
                @endif
                @if($settings->company_tax_number)
                    <p>Tax No: {{ $settings->company_tax_number }}</p>
                @endif
            </div>
            
            <div class="main-info">
                <div class="bill-to">
                    <h3>Quote For</h3>
                    <p><strong>{{ $quotation->client->display_name }}</strong></p>
                    @if($quotation->client->company_name && $quotation->client->company_name !== $quotation->client->name)
                        <p>{{ $quotation->client->company_name }}</p>
                    @endif
                    @if($quotation->client->full_address)
                        <p>{{ $quotation->client->full_address }}</p>
                    @endif
                    @if($quotation->client->email)
                        <p>{{ $quotation->client->email }}</p>
                    @endif
                </div>
                
                <div class="quotation-details">
                    <table>
                        <tr>
                            <td class="label">Quotation #</td>
                            <td class="value quotation-number">{{ $quotation->quotation_number }}</td>
                        </tr>
                        <tr>
                            <td class="label">Quotation Date</td>
                            <td class="value">{{ $quotation->quotation_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Valid Until</td>
                            <td class="value">{{ $quotation->valid_until->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->quotationItems as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->item_description)
                                <div class="item-description">{!! App\Models\Setting::formatDescription($item->item_description) !!}</div>
                            @endif
                        </td>
                        <td class="text-right">{!! $settings->formatCurrencyWithBreak($item->total_amount) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="totals">
                <table>
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">{!! $settings->formatCurrencyWithBreak($quotation->subtotal) !!}</td>
                    </tr>
                    @if($quotation->tax_amount > 0)
                    <tr>
                        <td class="label">Tax:</td>
                        <td class="amount">{!! $settings->formatCurrencyWithBreak($quotation->tax_amount) !!}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label">TOTAL:</td>
                        <td class="amount">{!! $settings->formatCurrencyWithBreak($quotation->total_amount) !!}</td>
                    </tr>
                </table>
            </div>
            
            <div class="clearfix"></div>
            
            @if($quotation->notes)
            <div class="footer-section">
                <h4>Notes</h4>
                <p>{{ $quotation->notes }}</p>
            </div>
            @endif
            
            <div class="footer-section">
                <h4>Terms & Conditions</h4>
                <p><strong>This quotation is valid until {{ $quotation->valid_until->format('M d, Y') }}</strong></p>
                <br>
                <p>• All prices are inclusive of applicable taxes unless otherwise specified</p>
                <p>• This quotation is subject to our standard terms and conditions</p>
                <p>• Payment terms: {{ $settings->default_payment_terms ?? 30 }} days from invoice date</p>
                <p>• Delivery timeline will be confirmed upon order confirmation</p>
                <br>
                <p>For questions regarding this quotation, please contact {{ $settings->company_email ?? 'mycosoftofficial@gmail.com' }}.</p>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('beforeprint', function() {
            document.querySelector('.print-controls').style.display = 'none';
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelector('.print-controls').style.display = 'block';
        });
    </script>
</body>
</html>