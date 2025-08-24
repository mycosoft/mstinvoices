<!-- Revenue Report PDF Content -->
<div class="section-title">Revenue Summary</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['total_revenue'], 0) }}</h3>
        <p>Total Revenue</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['paid_revenue'], 0) }}</h3>
        <p>Paid Revenue</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['pending_revenue'], 0) }}</h3>
        <p>Pending Revenue</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['invoice_count']) }}</h3>
        <p>Total Invoices</p>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['average_invoice_value'], 0) }}</h3>
        <p>Average Invoice Value</p>
    </div>
    <div class="summary-item">
        <h3>{{ $data['summary']['total_revenue'] > 0 ? number_format(($data['summary']['paid_revenue'] / $data['summary']['total_revenue']) * 100, 1) : 0 }}%</h3>
        <p>Payment Rate</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['total_revenue'] - $data['summary']['paid_revenue'], 0) }}</h3>
        <p>Outstanding Amount</p>
    </div>
    <div class="summary-item">
        <h3>{{ count($data['monthly_data']) }}</h3>
        <p>Reporting Periods</p>
    </div>
</div>

@if(count($data['monthly_data']) > 0)
<div class="section-title">Monthly Revenue Breakdown</div>

<table>
    <thead>
        <tr>
            <th>Month</th>
            <th>Year</th>
            <th class="text-right">Invoice Count</th>
            <th class="text-right">Total Revenue</th>
            <th class="text-right">Paid Revenue</th>
            <th class="text-right">Pending Revenue</th>
            <th class="text-right">Payment Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['monthly_data'] as $monthData)
        <tr>
            <td>{{ DateTime::createFromFormat('!m', $monthData->month)->format('F') }}</td>
            <td>{{ $monthData->year }}</td>
            <td class="text-right">{{ number_format($monthData->invoice_count) }}</td>
            <td class="text-right">UGX {{ number_format($monthData->total_revenue, 0) }}</td>
            <td class="text-right">UGX {{ number_format($monthData->paid_revenue, 0) }}</td>
            <td class="text-right">UGX {{ number_format($monthData->total_revenue - $monthData->paid_revenue, 0) }}</td>
            <td class="text-right">
                @if($monthData->total_revenue > 0)
                    <span class="badge badge-{{ ($monthData->paid_revenue / $monthData->total_revenue) >= 0.8 ? 'success' : (($monthData->paid_revenue / $monthData->total_revenue) >= 0.5 ? 'warning' : 'danger') }}">
                        {{ number_format(($monthData->paid_revenue / $monthData->total_revenue) * 100, 1) }}%
                    </span>
                @else
                    <span class="badge badge-secondary">0%</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f8f9fa; font-weight: bold;">
            <td colspan="2">Total</td>
            <td class="text-right">{{ number_format($data['monthly_data']->sum('invoice_count')) }}</td>
            <td class="text-right">UGX {{ number_format($data['monthly_data']->sum('total_revenue'), 0) }}</td>
            <td class="text-right">UGX {{ number_format($data['monthly_data']->sum('paid_revenue'), 0) }}</td>
            <td class="text-right">UGX {{ number_format($data['monthly_data']->sum('total_revenue') - $data['monthly_data']->sum('paid_revenue'), 0) }}</td>
            <td class="text-right">
                @php
                    $totalRevenue = $data['monthly_data']->sum('total_revenue');
                    $totalPaid = $data['monthly_data']->sum('paid_revenue');
                @endphp
                @if($totalRevenue > 0)
                    <span class="badge badge-{{ ($totalPaid / $totalRevenue) >= 0.8 ? 'success' : (($totalPaid / $totalRevenue) >= 0.5 ? 'warning' : 'danger') }}">
                        {{ number_format(($totalPaid / $totalRevenue) * 100, 1) }}%
                    </span>
                @else
                    <span class="badge badge-secondary">0%</span>
                @endif
            </td>
        </tr>
    </tfoot>
</table>
@endif

<div class="insights">
    <h4>Key Performance Insights</h4>
    @php
        $paymentRate = $data['summary']['total_revenue'] > 0 ? ($data['summary']['paid_revenue'] / $data['summary']['total_revenue']) * 100 : 0;
    @endphp
    <ul>
        <li><strong>Payment Collection Rate:</strong> {{ number_format($paymentRate, 1) }}% 
            @if($paymentRate >= 80)
                (Excellent - maintaining strong collection performance)
            @elseif($paymentRate >= 50)
                (Good - room for improvement in collections)
            @else
                (Critical - immediate attention required for collections)
            @endif
        </li>
        <li><strong>Average Invoice Value:</strong> UGX {{ number_format($data['summary']['average_invoice_value'], 0) }}
            @if($data['summary']['average_invoice_value'] < 100000)
                (Consider strategies to increase average transaction value)
            @else
                (Strong average transaction value)
            @endif
        </li>
        <li><strong>Outstanding Revenue:</strong> UGX {{ number_format($data['summary']['pending_revenue'], 0) }}
            @if($data['summary']['pending_revenue'] > 0)
                (Follow up required on pending invoices)
            @else
                (All invoices collected - excellent performance!)
            @endif
        </li>
        @if(count($data['monthly_data']) >= 2)
            @php
                $latest = $data['monthly_data']->first();
                $previous = $data['monthly_data']->skip(1)->first();
                $growth = $previous && $previous->total_revenue > 0 ? 
                    (($latest->total_revenue - $previous->total_revenue) / $previous->total_revenue) * 100 : 0;
            @endphp
            <li><strong>Recent Growth:</strong> 
                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                @if($growth > 10)
                    (Strong growth trajectory)
                @elseif($growth > 0)
                    (Positive growth trend)
                @else
                    (Review strategies to improve growth)
                @endif
            </li>
        @endif
    </ul>
</div>

<div class="insights">
    <h4>Strategic Recommendations</h4>
    <ul>
        @if($paymentRate < 70)
            <li>Implement stricter payment terms and automated follow-up procedures</li>
        @endif
        @if($data['summary']['pending_revenue'] > 1000000)
            <li>Prioritize collection of outstanding invoices - significant amount pending</li>
        @endif
        @if($data['summary']['average_invoice_value'] < 500000)
            <li>Explore upselling opportunities to increase average invoice values</li>
        @endif
        @if($paymentRate >= 80 && $data['summary']['pending_revenue'] <= 500000)
            <li>Excellent financial performance - maintain current strategies</li>
        @endif
        <li>Continue monitoring payment trends and adjust collection strategies as needed</li>
        <li>Consider offering early payment discounts to improve cash flow</li>
    </ul>
</div>