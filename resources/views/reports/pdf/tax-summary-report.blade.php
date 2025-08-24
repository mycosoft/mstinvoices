<!-- Tax Summary Report PDF Content -->
<div class="section-title">Tax Summary Overview</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['total_tax_collected'], 0) }}</h3>
        <p>Total Tax Collected</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['taxable_revenue'], 0) }}</h3>
        <p>Taxable Revenue</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['effective_tax_rate'], 2) }}%</h3>
        <p>Effective Tax Rate</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['taxable_invoices']) }}</h3>
        <p>Taxable Invoices</p>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['tax_paid'], 0) }}</h3>
        <p>Tax Paid</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['tax_outstanding'], 0) }}</h3>
        <p>Tax Outstanding</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['tax_payment_rate'], 1) }}%</h3>
        <p>Collection Rate</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['average_tax_per_invoice'], 0) }}</h3>
        <p>Avg Tax/Invoice</p>
    </div>
</div>

@if(isset($data['tax_rates']) && count($data['tax_rates']) > 0)
<div class="section-title">Tax Breakdown by Rate</div>

<table>
    <thead>
        <tr>
            <th>Tax Rate</th>
            <th class="text-right">Invoices</th>
            <th class="text-right">Taxable Amount</th>
            <th class="text-right">Tax Amount</th>
            <th class="text-right">Collected</th>
            <th class="text-right">Collection Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['tax_rates'] as $rate => $rateData)
        <tr>
            <td>
                <span class="badge badge-{{ $rate == 0 ? 'secondary' : ($rate <= 10 ? 'info' : ($rate <= 20 ? 'warning' : 'danger')) }}">
                    {{ number_format($rate, 1) }}%
                </span>
            </td>
            <td class="text-right">{{ number_format($rateData['invoice_count']) }}</td>
            <td class="text-right">UGX {{ number_format($rateData['taxable_amount'], 0) }}</td>
            <td class="text-right">UGX {{ number_format($rateData['tax_amount'], 0) }}</td>
            <td class="text-right">UGX {{ number_format($rateData['tax_collected'], 0) }}</td>
            <td class="text-right">
                <span class="badge badge-{{ $rateData['collection_rate'] >= 90 ? 'success' : ($rateData['collection_rate'] >= 70 ? 'warning' : 'danger') }}">
                    {{ number_format($rateData['collection_rate'], 1) }}%
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(isset($data['monthly_tax']) && count($data['monthly_tax']) > 0)
<div class="section-title">Monthly Tax Collection</div>

<table>
    <thead>
        <tr>
            <th>Month</th>
            <th class="text-right">Tax Due</th>
            <th class="text-right">Tax Collected</th>
            <th class="text-right">Outstanding</th>
            <th class="text-right">Collection Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['monthly_tax']->take(12) as $month)
        <tr>
            <td><strong>{{ DateTime::createFromFormat('!m', $month->month)->format('M') }} {{ $month->year }}</strong></td>
            <td class="text-right">UGX {{ number_format($month->tax_due, 0) }}</td>
            <td class="text-right">UGX {{ number_format($month->tax_collected, 0) }}</td>
            <td class="text-right">UGX {{ number_format($month->tax_due - $month->tax_collected, 0) }}</td>
            <td class="text-right">
                @php
                    $monthlyRate = $month->tax_due > 0 ? ($month->tax_collected / $month->tax_due) * 100 : 0;
                @endphp
                <span class="badge badge-{{ $monthlyRate >= 90 ? 'success' : ($monthlyRate >= 70 ? 'warning' : 'danger') }}">
                    {{ number_format($monthlyRate, 1) }}%
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="insights">
    <h4>Tax Compliance Analysis</h4>
    <ul>
        <li><strong>Collection Efficiency:</strong> {{ number_format($data['summary']['tax_payment_rate'], 1) }}% tax collection rate</li>
        <li><strong>Tax Burden:</strong> {{ number_format($data['summary']['effective_tax_rate'], 2) }}% effective tax rate on revenue</li>
        <li><strong>Outstanding Liability:</strong> UGX {{ number_format($data['summary']['tax_outstanding'], 0) }} in unpaid taxes</li>
        <li><strong>Compliance Status:</strong> {{ $data['summary']['tax_payment_rate'] >= 90 ? 'Excellent' : ($data['summary']['tax_payment_rate'] >= 70 ? 'Good' : 'Needs Improvement') }}</li>
    </ul>
</div>