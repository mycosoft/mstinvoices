<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt - {{ $sale->pos_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 11px; line-height: 1.3; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h3 { margin: 0; font-size: 14px; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 3px; }
        .total-row { font-weight: bold; }
        .footer { text-align: center; margin-top: 10px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h3>{{ $settings->company_name ?? 'Business Name' }}</h3>
        <p>{{ $settings->company_address ?? '' }}<br>
           {{ $settings->company_phone ?? '' }}<br>
           {{ $settings->company_email ?? '' }}</p>
        <h3>** RECEIPT **</h3>
        <p>POS #: {{ $sale->pos_number }}<br>
           Date: {{ $sale->sale_date->format('Y-m-d H:i') }}<br>
           Cashier: {{ $sale->cashier_name }}</p>
    </div>

    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th class="text-left">Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0) }}</td>
                <td class="text-right">{{ number_format($item->line_total, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <table>
        <tr><td>Subtotal</td><td class="text-right">{{ number_format($sale->subtotal, 0) }}</td></tr>
        @if($sale->tax_amount > 0)
        <tr><td>Tax</td><td class="text-right">{{ number_format($sale->tax_amount, 0) }}</td></tr>
        @endif
        @if($sale->discount_amount > 0)
        <tr><td>Discount</td><td class="text-right">({{ number_format($sale->discount_amount, 0) }})</td></tr>
        @endif
        <tr class="total-row"><td>TOTAL</td><td class="text-right">{{ number_format($sale->total_amount, 0) }}</td></tr>
        <tr><td>Paid</td><td class="text-right">{{ number_format($sale->paid_amount, 0) }}</td></tr>
        <tr><td>Change</td><td class="text-right">{{ number_format($sale->change_amount, 0) }}</td></tr>
        <tr><td>Method</td><td class="text-right">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td></tr>
    </table>

    <div class="line"></div>

    <div class="footer">
        <p>{{ $settings->default_invoice_footer ?? 'Thank you for your business!' }}</p>
        <p style="font-size:9px">{{ $sale->pos_number }} | {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
