<!-- Invoice Summary Report PDF Content -->
<div class="section-title">Invoice Summary Overview</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['total_invoices']) }}</h3>
        <p>Total Invoices</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['paid_invoices']) }}</h3>
        <p>Paid Invoices</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['pending_invoices']) }}</h3>
        <p>Pending Invoices</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['overdue_invoices']) }}</h3>
        <p>Overdue Invoices</p>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['total_value'], 0) }}</h3>
        <p>Total Value</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['average_value'], 0) }}</h3>
        <p>Average Value</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['payment_rate'], 1) }}%</h3>
        <p>Payment Rate</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['average_payment_days'] ?? 0, 0) }}</h3>
        <p>Avg Payment Days</p>
    </div>
</div>

@if(isset($data['status_breakdown']) && count($data['status_breakdown']) > 0)
<div class="section-title">Invoice Status Breakdown</div>

<table>
    <thead>
        <tr>
            <th>Status</th>
            <th class="text-right">Count</th>
            <th class="text-right">Value</th>
            <th class="text-right">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['status_breakdown'] as $status => $statusData)
        <tr>
            <td>
                <span class="badge badge-{{ 
                    $status === 'paid' ? 'success' : 
                    ($status === 'pending' ? 'warning' : 
                    ($status === 'overdue' ? 'danger' : 'secondary')) 
                }}">
                    {{ ucfirst($status) }}
                </span>
            </td>
            <td class="text-right">{{ number_format($statusData['count']) }}</td>
            <td class="text-right">UGX {{ number_format($statusData['value'], 0) }}</td>
            <td class="text-right">
                {{ number_format($data['summary']['total_invoices'] > 0 ? ($statusData['count'] / $data['summary']['total_invoices']) * 100 : 0, 1) }}%
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="insights">
    <h4>Invoice Management Performance</h4>
    <ul>
        <li><strong>Payment Efficiency:</strong> {{ number_format($data['summary']['payment_rate'], 1) }}% of invoices paid</li>
        <li><strong>Collection Time:</strong> {{ number_format($data['summary']['average_payment_days'] ?? 0, 0) }} days average payment time</li>
        <li><strong>Outstanding Management:</strong> {{ number_format($data['summary']['overdue_invoices']) }} overdue invoices requiring attention</li>
        <li><strong>Invoice Volume:</strong> {{ number_format($data['summary']['total_invoices']) }} total invoices processed</li>
    </ul>
</div>