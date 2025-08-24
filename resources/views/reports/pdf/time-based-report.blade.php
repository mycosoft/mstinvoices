<!-- Time-Based Report PDF Content -->
<div class="section-title">{{ ucfirst($report->type) }} Performance Analysis</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['total_revenue'], 0) }}</h3>
        <p>Total Revenue</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['total_invoices']) }}</h3>
        <p>Total Invoices</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['average_period_revenue'], 0) }}</h3>
        <p>Average {{ $report->type === 'monthly' ? 'Monthly' : 'Yearly' }}</p>
    </div>
    <div class="summary-item">
        <h3>{{ $data['summary']['growth_rate'] >= 0 ? '+' : '' }}{{ number_format($data['summary']['growth_rate'], 1) }}%</h3>
        <p>Growth Rate</p>
    </div>
</div>

@if(count($data['period_data']) > 0)
<div class="section-title">Detailed {{ ucfirst($report->type) }} Breakdown</div>

<table>
    <thead>
        <tr>
            @if($report->type === 'monthly')
                <th>Month</th>
                <th>Year</th>
            @else
                <th>Year</th>
            @endif
            <th class="text-right">Invoices</th>
            <th class="text-right">Total Revenue</th>
            <th class="text-right">Paid Revenue</th>
            <th class="text-right">Payment Rate</th>
            <th class="text-right">Avg Invoice Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['period_data']->take(20) as $period)
        <tr>
            @if($report->type === 'monthly')
                <td>{{ DateTime::createFromFormat('!m', $period->month)->format('M') }}</td>
                <td>{{ $period->year }}</td>
            @else
                <td><strong>{{ $period->year }}</strong></td>
            @endif
            <td class="text-right">{{ number_format($period->invoice_count) }}</td>
            <td class="text-right">UGX {{ number_format($period->total_revenue, 0) }}</td>
            <td class="text-right">UGX {{ number_format($period->paid_revenue, 0) }}</td>
            <td class="text-right">
                @php
                    $paymentRate = $period->total_revenue > 0 ? ($period->paid_revenue / $period->total_revenue) * 100 : 0;
                @endphp
                <span class="badge badge-{{ $paymentRate >= 80 ? 'success' : ($paymentRate >= 50 ? 'warning' : 'danger') }}">
                    {{ number_format($paymentRate, 1) }}%
                </span>
            </td>
            <td class="text-right">
                UGX {{ number_format($period->invoice_count > 0 ? $period->total_revenue / $period->invoice_count : 0, 0) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="insights">
    <h4>Performance Insights</h4>
    <ul>
        <li><strong>Growth Trend:</strong> {{ $data['summary']['growth_rate'] >= 0 ? 'Positive' : 'Negative' }} {{ abs($data['summary']['growth_rate']) }}% growth rate</li>
        <li><strong>Consistency:</strong> {{ $data['summary']['volatility_index'] <= 20 ? 'Very Stable' : ($data['summary']['volatility_index'] <= 40 ? 'Moderate Variation' : 'High Volatility') }}</li>
        @if($data['summary']['best_period'])
            <li><strong>Best Performance:</strong> 
                @if($report->type === 'monthly')
                    {{ DateTime::createFromFormat('!m', $data['summary']['best_period']->month)->format('M') }} {{ $data['summary']['best_period']->year }}
                @else
                    {{ $data['summary']['best_period']->year }}
                @endif
                (UGX {{ number_format($data['summary']['best_period']->total_revenue, 0) }})
            </li>
        @endif
    </ul>
</div>