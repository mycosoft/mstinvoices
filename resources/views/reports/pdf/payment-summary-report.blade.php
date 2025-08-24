<!-- Payment Summary Report PDF Content -->
<div class="section-title">Payment Summary Overview</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['total_payments'], 0) }}</h3>
        <p>Total Payments</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['payment_count']) }}</h3>
        <p>Payment Count</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['average_payment'], 0) }}</h3>
        <p>Average Payment</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['average_payment_days'], 0) }}</h3>
        <p>Avg Payment Days</p>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['early_payments']) }}</h3>
        <p>Early Payments ({{ number_format($data['summary']['early_payment_rate'], 1) }}%)</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['ontime_payments']) }}</h3>
        <p>On-Time Payments ({{ number_format($data['summary']['ontime_payment_rate'], 1) }}%)</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['late_payments']) }}</h3>
        <p>Late Payments ({{ number_format($data['summary']['late_payment_rate'], 1) }}%)</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['ontime_payment_rate'] + $data['summary']['early_payment_rate'], 1) }}%</h3>
        <p>Overall Compliance</p>
    </div>
</div>

@if(isset($data['payment_methods']) && count($data['payment_methods']) > 0)
<div class="section-title">Payment Method Analysis</div>

<table>
    <thead>
        <tr>
            <th>Payment Method</th>
            <th class="text-right">Count</th>
            <th class="text-right">Amount</th>
            <th class="text-right">Avg Days</th>
            <th class="text-right">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['payment_methods'] as $method => $methodData)
        <tr>
            <td>
                <span class="badge badge-{{ 
                    $method === 'cash' ? 'success' : 
                    ($method === 'bank_transfer' ? 'primary' : 
                    ($method === 'mobile_money' ? 'info' : 'secondary')) 
                }}">
                    {{ ucfirst(str_replace('_', ' ', $method)) }}
                </span>
            </td>
            <td class="text-right">{{ number_format($methodData['count']) }}</td>
            <td class="text-right">UGX {{ number_format($methodData['amount'], 0) }}</td>
            <td class="text-right">{{ number_format($methodData['avg_days'], 1) }}</td>
            <td class="text-right">{{ number_format($methodData['percentage'], 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="insights">
    <h4>Payment Performance Analysis</h4>
    <ul>
        <li><strong>Payment Timing:</strong> {{ number_format($data['summary']['ontime_payment_rate'], 1) }}% on-time payment rate</li>
        <li><strong>Collection Efficiency:</strong> {{ number_format($data['summary']['average_payment_days'], 0) }} days average collection time</li>
        <li><strong>Early Payment Incentive:</strong> {{ number_format($data['summary']['early_payment_rate'], 1) }}% early payments</li>
        <li><strong>Payment Reliability:</strong> {{ number_format($data['summary']['late_payment_rate'], 1) }}% late payments requiring follow-up</li>
    </ul>
</div>