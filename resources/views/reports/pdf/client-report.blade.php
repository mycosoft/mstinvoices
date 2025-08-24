<!-- Client Report PDF Content -->
<div class="section-title">Client Performance Summary</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['total_clients']) }}</h3>
        <p>Total Clients</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['active_clients']) }}</h3>
        <p>Active Clients</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['total_revenue'], 0) }}</h3>
        <p>Total Revenue</p>
    </div>
    <div class="summary-item">
        <h3>UGX {{ number_format($data['summary']['average_revenue_per_client'], 0) }}</h3>
        <p>Avg Revenue/Client</p>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['total_invoices']) }}</h3>
        <p>Total Invoices</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['average_invoices_per_client'], 1) }}</h3>
        <p>Avg Invoices/Client</p>
    </div>
    <div class="summary-item">
        <h3>{{ number_format($data['summary']['average_payment_days'], 0) }}</h3>
        <p>Avg Payment Days</p>
    </div>
    <div class="summary-item">
        <h3>{{ $data['summary']['total_clients'] > 0 ? number_format(($data['summary']['active_clients'] / $data['summary']['total_clients']) * 100, 1) : 0 }}%</h3>
        <p>Client Retention</p>
    </div>
</div>

@if(count($data['top_clients']) > 0)
<div class="section-title">Top Performing Clients</div>

<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Client Name</th>
            <th>Company</th>
            <th class="text-right">Total Revenue</th>
            <th class="text-right">Invoice Count</th>
            <th class="text-right">Avg Invoice Value</th>
            <th class="text-right">Payment Rate</th>
            <th class="text-right">Avg Payment Days</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['top_clients']->take(15) as $index => $client)
        <tr>
            <td class="text-center">
                @if($index < 3)
                    <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'info' : 'secondary') }}">
                        {{ $index + 1 }}
                    </span>
                @else
                    {{ $index + 1 }}
                @endif
            </td>
            <td><strong>{{ $client->name }}</strong></td>
            <td>{{ $client->company ?? 'Individual' }}</td>
            <td class="text-right"><strong>UGX {{ number_format($client->total_revenue, 0) }}</strong></td>
            <td class="text-right">{{ number_format($client->invoice_count) }}</td>
            <td class="text-right">UGX {{ number_format($client->average_invoice_value, 0) }}</td>
            <td class="text-right">
                @php
                    $paymentRate = $client->total_revenue > 0 ? ($client->paid_revenue / $client->total_revenue) * 100 : 0;
                @endphp
                <span class="badge badge-{{ $paymentRate >= 80 ? 'success' : ($paymentRate >= 50 ? 'warning' : 'danger') }}">
                    {{ number_format($paymentRate, 1) }}%
                </span>
            </td>
            <td class="text-right">
                <span class="badge badge-{{ $client->average_payment_days <= 30 ? 'success' : ($client->average_payment_days <= 60 ? 'warning' : 'danger') }}">
                    {{ number_format($client->average_payment_days, 0) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f8f9fa; font-weight: bold;">
            <td colspan="3">Total (Top {{ $data['top_clients']->take(15)->count() }} Clients)</td>
            <td class="text-right">UGX {{ number_format($data['top_clients']->take(15)->sum('total_revenue'), 0) }}</td>
            <td class="text-right">{{ number_format($data['top_clients']->take(15)->sum('invoice_count')) }}</td>
            <td class="text-right">UGX {{ number_format($data['top_clients']->take(15)->avg('average_invoice_value'), 0) }}</td>
            <td class="text-right">
                @php
                    $topTotalRevenue = $data['top_clients']->take(15)->sum('total_revenue');
                    $topTotalPaid = $data['top_clients']->take(15)->sum('paid_revenue');
                    $topOverallRate = $topTotalRevenue > 0 ? ($topTotalPaid / $topTotalRevenue) * 100 : 0;
                @endphp
                <span class="badge badge-{{ $topOverallRate >= 80 ? 'success' : ($topOverallRate >= 50 ? 'warning' : 'danger') }}">
                    {{ number_format($topOverallRate, 1) }}%
                </span>
            </td>
            <td class="text-right">{{ number_format($data['top_clients']->take(15)->avg('average_payment_days'), 0) }}</td>
        </tr>
    </tfoot>
</table>
@endif

<div class="page-break"></div>

<div class="section-title">Client Segmentation Analysis</div>

<table>
    <thead>
        <tr>
            <th>Revenue Segment</th>
            <th class="text-right">Client Count</th>
            <th class="text-right">Total Revenue</th>
            <th class="text-right">Percentage of Total</th>
            <th class="text-right">Avg Revenue/Client</th>
        </tr>
    </thead>
    <tbody>
        @php
            $segments = [
                'High Value (>1M)' => ['min' => 1000000, 'color' => 'success'],
                'Medium Value (500K-1M)' => ['min' => 500000, 'max' => 999999, 'color' => 'warning'],
                'Low Value (100K-500K)' => ['min' => 100000, 'max' => 499999, 'color' => 'info'],
                'New/Small (<100K)' => ['max' => 99999, 'color' => 'secondary']
            ];
            $totalRevenue = $data['summary']['total_revenue'];
        @endphp
        @foreach($segments as $name => $criteria)
            @php
                $segmentClients = $data['top_clients']->filter(function($client) use ($criteria) {
                    $revenue = $client->total_revenue;
                    $minMet = !isset($criteria['min']) || $revenue >= $criteria['min'];
                    $maxMet = !isset($criteria['max']) || $revenue <= $criteria['max'];
                    return $minMet && $maxMet;
                });
                $segmentRevenue = $segmentClients->sum('total_revenue');
                $percentage = $totalRevenue > 0 ? ($segmentRevenue / $totalRevenue) * 100 : 0;
                $avgRevenue = $segmentClients->count() > 0 ? $segmentRevenue / $segmentClients->count() : 0;
            @endphp
            <tr>
                <td><span class="badge badge-{{ $criteria['color'] }}">{{ $name }}</span></td>
                <td class="text-right">{{ $segmentClients->count() }}</td>
                <td class="text-right">UGX {{ number_format($segmentRevenue, 0) }}</td>
                <td class="text-right">{{ number_format($percentage, 1) }}%</td>
                <td class="text-right">UGX {{ number_format($avgRevenue, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="section-title">Payment Behavior Analysis</div>

<table>
    <thead>
        <tr>
            <th>Payment Speed Category</th>
            <th class="text-right">Client Count</th>
            <th class="text-right">Avg Payment Days</th>
            <th class="text-right">Revenue Share</th>
            <th>Performance Level</th>
        </tr>
    </thead>
    <tbody>
        @php
            $paymentCategories = [
                'Excellent (≤15 days)' => ['max' => 15, 'color' => 'success'],
                'Good (16-30 days)' => ['min' => 16, 'max' => 30, 'color' => 'info'],
                'Average (31-60 days)' => ['min' => 31, 'max' => 60, 'color' => 'warning'],
                'Slow (>60 days)' => ['min' => 61, 'color' => 'danger']
            ];
        @endphp
        @foreach($paymentCategories as $category => $criteria)
            @php
                $categoryClients = $data['top_clients']->filter(function($client) use ($criteria) {
                    $days = $client->average_payment_days;
                    $minMet = !isset($criteria['min']) || $days >= $criteria['min'];
                    $maxMet = !isset($criteria['max']) || $days <= $criteria['max'];
                    return $minMet && $maxMet;
                });
                $categoryRevenue = $categoryClients->sum('total_revenue');
                $revenueShare = $totalRevenue > 0 ? ($categoryRevenue / $totalRevenue) * 100 : 0;
                $avgDays = $categoryClients->avg('average_payment_days');
            @endphp
            @if($categoryClients->count() > 0)
            <tr>
                <td>{{ $category }}</td>
                <td class="text-right">{{ $categoryClients->count() }}</td>
                <td class="text-right">{{ number_format($avgDays, 0) }}</td>
                <td class="text-right">{{ number_format($revenueShare, 1) }}%</td>
                <td><span class="badge badge-{{ $criteria['color'] }}">{{ ucfirst(explode(' ', $category)[0]) }}</span></td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>

<div class="insights">
    <h4>Client Portfolio Analysis</h4>
    @php
        $clientRetentionRate = $data['summary']['total_clients'] > 0 ? ($data['summary']['active_clients'] / $data['summary']['total_clients']) * 100 : 0;
        $revenueConcentration = $data['top_clients']->take(5)->sum('total_revenue');
        $concentrationRate = $data['summary']['total_revenue'] > 0 ? ($revenueConcentration / $data['summary']['total_revenue']) * 100 : 0;
    @endphp
    <ul>
        <li><strong>Client Retention Rate:</strong> {{ number_format($clientRetentionRate, 1) }}%
            @if($clientRetentionRate >= 80)
                (Excellent client retention)
            @elseif($clientRetentionRate >= 60)
                (Good retention with room for improvement)
            @else
                (Focus needed on client retention strategies)
            @endif
        </li>
        <li><strong>Revenue Concentration:</strong> Top 5 clients represent {{ number_format($concentrationRate, 1) }}% of revenue
            @if($concentrationRate > 80)
                (High concentration risk - diversify client base)
            @elseif($concentrationRate > 60)
                (Moderate concentration - monitor closely)
            @else
                (Well-diversified client portfolio)
            @endif
        </li>
        <li><strong>Average Revenue per Client:</strong> UGX {{ number_format($data['summary']['average_revenue_per_client'], 0) }}
            @if($data['summary']['average_revenue_per_client'] > 1000000)
                (High-value client base)
            @else
                (Opportunity to increase client values)
            @endif
        </li>
        <li><strong>Payment Performance:</strong> {{ number_format($data['summary']['average_payment_days'], 0) }} days average
            @if($data['summary']['average_payment_days'] <= 30)
                (Excellent payment discipline)
            @elseif($data['summary']['average_payment_days'] <= 45)
                (Good payment behavior)
            @else
                (Payment terms need attention)
            @endif
        </li>
    </ul>
</div>

<div class="insights">
    <h4>Strategic Recommendations</h4>
    <ul>
        @if($concentrationRate > 80)
            <li>Diversify client base to reduce revenue concentration risk</li>
        @endif
        @if($clientRetentionRate < 80)
            <li>Implement client retention programs and regular satisfaction surveys</li>
        @endif
        @if($data['summary']['average_payment_days'] > 45)
            <li>Review and tighten payment terms for new client agreements</li>
        @endif
        @if($data['summary']['average_revenue_per_client'] < 500000)
            <li>Develop upselling strategies for existing clients</li>
        @endif
        <li>Focus on acquiring clients in the medium-to-high value segments</li>
        <li>Implement loyalty programs for top-performing clients</li>
        <li>Regular client relationship management and performance reviews</li>
    </ul>
</div>