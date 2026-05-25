<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            color: #000;
            background-color: #fff;
            font-size: 12px;
            line-height: 1.4;
        }
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
            background: white;
            border: 1px solid #000;
            padding: 15px;
        }
        .company-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .company-services {
            font-size: 10px;
            margin-bottom: 5px;
            line-height: 1.2;
        }
        .company-contact {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .receipt-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            border: 1px solid #000;
            padding: 5px;
            background-color: #f0f0f0;
        }
        .receipt-details {
            margin-bottom: 15px;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 2px;
        }
        .receipt-label {
            font-weight: bold;
            min-width: 80px;
        }
        .receipt-value {
            text-align: right;
            flex: 1;
        }
        .amount-section {
            text-align: center;
            margin: 15px 0;
            border: 1px solid #000;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .amount-figures {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .amount-words {
            font-size: 11px;
            font-style: italic;
        }
        .payment-method {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        .balance-section {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .signature-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .signature-label {
            font-weight: bold;
        }
        .signature-space {
            border-bottom: 1px solid #000;
            width: 100px;
            height: 20px;
            margin-left: 10px;
        }
        .footer-info {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .with-thanks {
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
        }
        .company-stamp {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
        }
        @media print {
            body { margin: 0; padding: 5px; }
            .receipt-container { border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-name">{{ $settings->company_name ?? 'MYCOSOFT TECHNOLOGIES' }}</div>
            <div class="company-services">
                Computer Networking, Hosting and Web-Marketing,<br>
                Customised Software Development<br>
                Website Designing & Development
            </div>
            <div class="company-contact">
                Tel: {{ $settings->company_phone ?? '0750 501151 / 0781779 477' }}<br>
                Email: {{ $settings->company_email ?? 'mycosoftofficial@gmail.com' }}<br>
                {{ $settings->company_address ?? 'Kyengera Nabaziza' }}
            </div>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">RECEIPT</div>

        <!-- Receipt Details -->
        <div class="receipt-details">
            <div class="receipt-row">
                <span class="receipt-label">Receipt No:</span>
                <span class="receipt-value">{{ str_pad($payment->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Date:</span>
                <span class="receipt-value">{{ $payment->payment_date->format('d/m/Y') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Received with thanks from:</span>
                <span class="receipt-value">{{ $invoice->client->display_name ?? 'Client' }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">The sum of shillings:</span>
                <span class="receipt-value">{{ number_format($payment->amount, 0) }} shillings</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Being payment of:</span>
                <span class="receipt-value">Invoice {{ $invoice->invoice_number }}</span>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-figures">Shs. {{ number_format($payment->amount, 0) }}/=</div>
            <div class="amount-words">({{ number_format($payment->amount, 0) }} shillings only)</div>
        </div>

        <!-- Payment Method -->
        <div class="payment-method">
            Cash / Cheque No.: 
            @if($payment->payment_method == 'cash')
                Cash
            @elseif($payment->payment_method == 'cheque')
                Cheque
            @elseif($payment->payment_method == 'bank_transfer')
                Bank Transfer
            @elseif($payment->payment_method == 'mobile_money')
                Mobile Money
            @else
                {{ ucfirst($payment->payment_method) }}
            @endif
        </div>

        <!-- Balance -->
        <div class="balance-section">
            Balance: {{ $invoice->balance_due > 0 ? 'Shs. ' . number_format($invoice->balance_due, 0) . '/=' : 'NIL' }}
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-line">
                <span class="signature-label">Signature:</span>
                <div class="signature-space"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <div class="with-thanks">With Thanks</div>
            <div class="company-stamp">
                For: {{ $settings->company_name ?? 'MYCOSOFT TECHNOLOGIES' }}<br>
                TEL: {{ $settings->company_phone ?? '0750501151' }}<br>
                {{ $settings->company_email ?? 'mycosoftech@' }}
            </div>
        </div>
    </div>
</body>
</html>
