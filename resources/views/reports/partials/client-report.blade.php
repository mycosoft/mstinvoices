<!-- Client Performance Report -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i> Client Performance Summary
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Clients</span>
                            <span class="info-box-number">{{ number_format($data['summary']['total_clients']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Clients</span>
                            <span class="info-box-number">{{ number_format($data['summary']['active_clients']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['total_revenue'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-calculator"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Revenue/Client</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['average_revenue_per_client'], 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-file-invoice"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Invoices</span>
                            <span class="info-box-number">{{ number_format($data['summary']['total_invoices']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-dark"><i class="fas fa-receipt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Invoices/Client</span>
                            <span class="info-box-number">{{ number_format($data['summary']['average_invoices_per_client'], 1) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Payment Days</span>
                            <span class="info-box-number">{{ number_format($data['summary']['average_payment_days'], 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($report->format === 'chart')
<!-- Client Performance Chart -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Client Revenue Distribution
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="clientChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@if($report->format === 'table' || $report->format === 'summary')
<!-- Top Performing Clients -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-trophy"></i> Top Performing Clients
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Client Name</th>
                            <th>Contact</th>
                            <th class="text-right">Total Revenue</th>
                            <th class="text-right">Invoice Count</th>
                            <th class="text-right">Avg Invoice Value</th>
                            <th class="text-right">Payment Rate</th>
                            <th class="text-right">Avg Payment Days</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['top_clients'] as $index => $client)
                        <tr>
                            <td>
                                @if($index < 3)
                                    <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'light' : 'secondary') }}">
                                        <i class="fas fa-{{ $index === 0 ? 'crown' : ($index === 1 ? 'medal' : 'award') }}"></i>
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    <span class="badge badge-dark">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $client->name }}</strong>
                                <br><small class="text-muted">{{ $client->company ?? 'Individual' }}</small>
                            </td>
                            <td>
                                <small>
                                    @if($client->email)
                                        <i class="fas fa-envelope text-info"></i> {{ $client->email }}<br>
                                    @endif
                                    @if($client->phone)
                                        <i class="fas fa-phone text-success"></i> {{ $client->phone }}
                                    @endif
                                </small>
                            </td>
                            <td class="text-right">
                                <strong>UGX {{ number_format($client->total_revenue, 0) }}</strong>
                            </td>
                            <td class="text-right">
                                <span class="badge badge-info">{{ number_format($client->invoice_count) }}</span>
                            </td>
                            <td class="text-right">
                                UGX {{ number_format($client->average_invoice_value, 0) }}
                            </td>
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
                                    {{ number_format($client->average_payment_days, 0) }} days
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $client->is_active ? 'success' : 'secondary' }}">
                                    {{ $client->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No client data found for the selected period</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($data['top_clients']->count() > 0)
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="3">Total</td>
                            <td class="text-right">UGX {{ number_format($data['top_clients']->sum('total_revenue'), 0) }}</td>
                            <td class="text-right">{{ number_format($data['top_clients']->sum('invoice_count')) }}</td>
                            <td class="text-right">UGX {{ number_format($data['top_clients']->avg('average_invoice_value'), 0) }}</td>
                            <td class="text-right">
                                @php
                                    $totalRevenue = $data['top_clients']->sum('total_revenue');
                                    $totalPaid = $data['top_clients']->sum('paid_revenue');
                                    $overallRate = $totalRevenue > 0 ? ($totalPaid / $totalRevenue) * 100 : 0;
                                @endphp
                                <span class="badge badge-{{ $overallRate >= 80 ? 'success' : ($overallRate >= 50 ? 'warning' : 'danger') }}">
                                    {{ number_format($overallRate, 1) }}%
                                </span>
                            </td>
                            <td class="text-right">{{ number_format($data['top_clients']->avg('average_payment_days'), 0) }} days</td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Client Segmentation -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-pie"></i> Client Segmentation by Revenue
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Segment</th>
                            <th class="text-right">Clients</th>
                            <th class="text-right">Revenue</th>
                            <th class="text-right">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $segments = [
                                'High Value' => ['min' => 1000000, 'color' => 'success'],
                                'Medium Value' => ['min' => 500000, 'max' => 999999, 'color' => 'warning'],
                                'Low Value' => ['min' => 100000, 'max' => 499999, 'color' => 'info'],
                                'New/Small' => ['max' => 99999, 'color' => 'secondary']
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
                            @endphp
                            @if($segmentClients->count() > 0)
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $criteria['color'] }}">{{ $name }}</span>
                                </td>
                                <td class="text-right">{{ $segmentClients->count() }}</td>
                                <td class="text-right">UGX {{ number_format($segmentRevenue, 0) }}</td>
                                <td class="text-right">{{ number_format($percentage, 1) }}%</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Behavior Analysis -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clock"></i> Payment Behavior Analysis
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Payment Speed</th>
                            <th class="text-right">Clients</th>
                            <th class="text-right">Avg Days</th>
                            <th class="text-right">Revenue Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $paymentCategories = [
                                'Excellent' => ['max' => 15, 'color' => 'success'],
                                'Good' => ['min' => 16, 'max' => 30, 'color' => 'info'],
                                'Average' => ['min' => 31, 'max' => 60, 'color' => 'warning'],
                                'Slow' => ['min' => 61, 'color' => 'danger']
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
                                <td>
                                    <span class="badge badge-{{ $criteria['color'] }}">{{ $category }}</span>
                                </td>
                                <td class="text-right">{{ $categoryClients->count() }}</td>
                                <td class="text-right">{{ number_format($avgDays, 0) }}</td>
                                <td class="text-right">{{ number_format($revenueShare, 1) }}%</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Key Insights and Recommendations -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lightbulb"></i> Client Analysis & Recommendations
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Key Performance Indicators</h5>
                    <ul class="list-unstyled">
                        @php
                            $clientRetentionRate = $data['summary']['total_clients'] > 0 ? ($data['summary']['active_clients'] / $data['summary']['total_clients']) * 100 : 0;
                            $revenueConcentration = $data['top_clients']->take(5)->sum('total_revenue');
                            $concentrationRate = $data['summary']['total_revenue'] > 0 ? ($revenueConcentration / $data['summary']['total_revenue']) * 100 : 0;
                        @endphp
                        <li>
                            <i class="fas fa-circle text-{{ $clientRetentionRate >= 80 ? 'success' : ($clientRetentionRate >= 60 ? 'warning' : 'danger') }}"></i>
                            Client retention rate: <strong>{{ number_format($clientRetentionRate, 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-{{ $concentrationRate <= 60 ? 'success' : ($concentrationRate <= 80 ? 'warning' : 'danger') }}"></i>
                            Top 5 client concentration: <strong>{{ number_format($concentrationRate, 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-info"></i>
                            Average revenue per client: <strong>UGX {{ number_format($data['summary']['average_revenue_per_client'], 0) }}</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-primary"></i>
                            Average payment period: <strong>{{ number_format($data['summary']['average_payment_days'], 0) }} days</strong>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Strategic Recommendations</h5>
                    <ul class="list-unstyled">
                        @if($concentrationRate > 80)
                            <li><i class="fas fa-exclamation-triangle text-danger"></i> High revenue concentration risk - diversify client base</li>
                        @endif
                        @if($clientRetentionRate < 60)
                            <li><i class="fas fa-heart text-warning"></i> Focus on client retention strategies</li>
                        @endif
                        @if($data['summary']['average_payment_days'] > 45)
                            <li><i class="fas fa-clock text-danger"></i> Implement stricter payment terms and follow-up procedures</li>
                        @endif
                        @if($data['summary']['average_revenue_per_client'] < 500000)
                            <li><i class="fas fa-arrow-up text-info"></i> Explore upselling opportunities with existing clients</li>
                        @endif
                        @if($clientRetentionRate >= 80 && $concentrationRate <= 60)
                            <li><i class="fas fa-thumbs-up text-success"></i> Excellent client portfolio balance!</li>
                        @endif
                        <li><i class="fas fa-target text-primary"></i> Consider loyalty programs for top-performing clients</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
@parent
<script>
$(document).ready(function() {
    @if($report->format === 'chart' && isset($data['top_clients']))
        initializeClientChart();
    @endif
});

function initializeClientChart() {
    const ctx = document.getElementById('clientChart');
    if (!ctx) return;
    
    const clientData = @json($data['top_clients']->take(10)->values());
    
    new Chart(ctx, {
        type: '{{ $report->chart_type ?? "bar" }}',
        data: {
            labels: clientData.map(client => client.name),
            datasets: [{
                label: 'Total Revenue',
                data: clientData.map(client => client.total_revenue),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Paid Revenue',
                data: clientData.map(client => client.paid_revenue),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': UGX ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection