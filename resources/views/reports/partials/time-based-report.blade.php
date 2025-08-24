<!-- Time-Based Report (Monthly/Yearly) -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt"></i> {{ ucfirst($report->type) }} Performance Summary
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['total_revenue'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Invoices</span>
                            <span class="info-box-number">{{ number_format($data['summary']['total_invoices']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-calculator"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Average {{ $report->type === 'monthly' ? 'Monthly' : 'Yearly' }}</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['average_period_revenue'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Growth Rate</span>
                            <span class="info-box-number {{ $data['summary']['growth_rate'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $data['summary']['growth_rate'] >= 0 ? '+' : '' }}{{ number_format($data['summary']['growth_rate'], 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-arrow-up"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Best {{ $report->type === 'monthly' ? 'Month' : 'Year' }}</span>
                            <span class="info-box-number">
                                @if($data['summary']['best_period'])
                                    @if($report->type === 'monthly')
                                        {{ DateTime::createFromFormat('!m', $data['summary']['best_period']->month)->format('M') }} {{ $data['summary']['best_period']->year }}
                                    @else
                                        {{ $data['summary']['best_period']->year }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </span>
                            @if($data['summary']['best_period'])
                                <div class="info-box-more">UGX {{ number_format($data['summary']['best_period']->total_revenue, 0) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-dark"><i class="fas fa-arrow-down"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Worst {{ $report->type === 'monthly' ? 'Month' : 'Year' }}</span>
                            <span class="info-box-number">
                                @if($data['summary']['worst_period'])
                                    @if($report->type === 'monthly')
                                        {{ DateTime::createFromFormat('!m', $data['summary']['worst_period']->month)->format('M') }} {{ $data['summary']['worst_period']->year }}
                                    @else
                                        {{ $data['summary']['worst_period']->year }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </span>
                            @if($data['summary']['worst_period'])
                                <div class="info-box-more">UGX {{ number_format($data['summary']['worst_period']->total_revenue, 0) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-chart-area"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Volatility Index</span>
                            <span class="info-box-number">{{ number_format($data['summary']['volatility_index'], 1) }}%</span>
                            <div class="info-box-more">
                                {{ $data['summary']['volatility_index'] <= 20 ? 'Low' : ($data['summary']['volatility_index'] <= 40 ? 'Medium' : 'High') }} Risk
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($report->format === 'chart')
<!-- Time-Based Revenue Chart -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> {{ ucfirst($report->type) }} Revenue Trend
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="timeTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Metrics Chart -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Invoice Volume Trend
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 300px;">
                <canvas id="invoiceVolumeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-pie"></i> Payment Rate Trend
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 300px;">
                <canvas id="paymentRateChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@if($report->format === 'table' || $report->format === 'summary')
<!-- Detailed Period Breakdown -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i> Detailed {{ ucfirst($report->type) }} Breakdown
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            @if($report->type === 'monthly')
                                <th>Month</th>
                                <th>Year</th>
                            @else
                                <th>Year</th>
                            @endif
                            <th class="text-right">Invoice Count</th>
                            <th class="text-right">Total Revenue</th>
                            <th class="text-right">Paid Revenue</th>
                            <th class="text-right">Pending Revenue</th>
                            <th class="text-right">Payment Rate</th>
                            <th class="text-right">Avg Invoice Value</th>
                            <th class="text-right">Growth Rate</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['period_data'] as $index => $period)
                        @php
                            $paymentRate = $period->total_revenue > 0 ? ($period->paid_revenue / $period->total_revenue) * 100 : 0;
                            $avgInvoiceValue = $period->invoice_count > 0 ? $period->total_revenue / $period->invoice_count : 0;
                            $previousPeriod = $data['period_data'][$index + 1] ?? null;
                            $growthRate = 0;
                            if ($previousPeriod && $previousPeriod->total_revenue > 0) {
                                $growthRate = (($period->total_revenue - $previousPeriod->total_revenue) / $previousPeriod->total_revenue) * 100;
                            }
                        @endphp
                        <tr>
                            @if($report->type === 'monthly')
                                <td>{{ DateTime::createFromFormat('!m', $period->month)->format('F') }}</td>
                                <td>{{ $period->year }}</td>
                            @else
                                <td><strong>{{ $period->year }}</strong></td>
                            @endif
                            <td class="text-right">
                                <span class="badge badge-info">{{ number_format($period->invoice_count) }}</span>
                            </td>
                            <td class="text-right">
                                <strong>UGX {{ number_format($period->total_revenue, 0) }}</strong>
                            </td>
                            <td class="text-right">
                                UGX {{ number_format($period->paid_revenue, 0) }}
                            </td>
                            <td class="text-right">
                                UGX {{ number_format($period->total_revenue - $period->paid_revenue, 0) }}
                            </td>
                            <td class="text-right">
                                <span class="badge badge-{{ $paymentRate >= 80 ? 'success' : ($paymentRate >= 50 ? 'warning' : 'danger') }}">
                                    {{ number_format($paymentRate, 1) }}%
                                </span>
                            </td>
                            <td class="text-right">
                                UGX {{ number_format($avgInvoiceValue, 0) }}
                            </td>
                            <td class="text-right">
                                @if($previousPeriod)
                                    <span class="badge badge-{{ $growthRate >= 0 ? 'success' : 'danger' }}">
                                        {{ $growthRate >= 0 ? '+' : '' }}{{ number_format($growthRate, 1) }}%
                                    </span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $performance = 'average';
                                    $performanceColor = 'warning';
                                    $performanceIcon = 'fas fa-minus';
                                    
                                    if ($period->total_revenue >= $data['summary']['average_period_revenue'] * 1.2) {
                                        $performance = 'excellent';
                                        $performanceColor = 'success';
                                        $performanceIcon = 'fas fa-arrow-up';
                                    } elseif ($period->total_revenue >= $data['summary']['average_period_revenue'] * 1.1) {
                                        $performance = 'good';
                                        $performanceColor = 'info';
                                        $performanceIcon = 'fas fa-thumbs-up';
                                    } elseif ($period->total_revenue <= $data['summary']['average_period_revenue'] * 0.8) {
                                        $performance = 'poor';
                                        $performanceColor = 'danger';
                                        $performanceIcon = 'fas fa-arrow-down';
                                    }
                                @endphp
                                <span class="badge badge-{{ $performanceColor }}">
                                    <i class="{{ $performanceIcon }}"></i> {{ ucfirst($performance) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $report->type === 'monthly' ? '10' : '9' }}" class="text-center text-muted">
                                No data found for the selected period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($data['period_data']->count() > 0)
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="{{ $report->type === 'monthly' ? '2' : '1' }}">Total</td>
                            <td class="text-right">{{ number_format($data['period_data']->sum('invoice_count')) }}</td>
                            <td class="text-right">UGX {{ number_format($data['period_data']->sum('total_revenue'), 0) }}</td>
                            <td class="text-right">UGX {{ number_format($data['period_data']->sum('paid_revenue'), 0) }}</td>
                            <td class="text-right">UGX {{ number_format($data['period_data']->sum('total_revenue') - $data['period_data']->sum('paid_revenue'), 0) }}</td>
                            <td class="text-right">
                                @php
                                    $totalRevenue = $data['period_data']->sum('total_revenue');
                                    $totalPaid = $data['period_data']->sum('paid_revenue');
                                    $overallRate = $totalRevenue > 0 ? ($totalPaid / $totalRevenue) * 100 : 0;
                                @endphp
                                <span class="badge badge-{{ $overallRate >= 80 ? 'success' : ($overallRate >= 50 ? 'warning' : 'danger') }}">
                                    {{ number_format($overallRate, 1) }}%
                                </span>
                            </td>
                            <td class="text-right">
                                @php
                                    $totalInvoices = $data['period_data']->sum('invoice_count');
                                    $avgValue = $totalInvoices > 0 ? $totalRevenue / $totalInvoices : 0;
                                @endphp
                                UGX {{ number_format($avgValue, 0) }}
                            </td>
                            <td class="text-right">{{ number_format($data['summary']['growth_rate'], 1) }}%</td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Seasonal Analysis (for monthly reports) -->
@if($report->type === 'monthly' && $data['period_data']->count() >= 12)
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-snowflake"></i> Seasonal Performance
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Season</th>
                            <th class="text-right">Avg Revenue</th>
                            <th class="text-right">Best Month</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $seasons = [
                                'Q1 (Jan-Mar)' => [1, 2, 3],
                                'Q2 (Apr-Jun)' => [4, 5, 6],
                                'Q3 (Jul-Sep)' => [7, 8, 9],
                                'Q4 (Oct-Dec)' => [10, 11, 12]
                            ];
                        @endphp
                        @foreach($seasons as $seasonName => $months)
                            @php
                                $seasonData = $data['period_data']->whereIn('month', $months);
                                $avgRevenue = $seasonData->avg('total_revenue');
                                $bestMonth = $seasonData->sortByDesc('total_revenue')->first();
                            @endphp
                            @if($seasonData->count() > 0)
                            <tr>
                                <td><strong>{{ $seasonName }}</strong></td>
                                <td class="text-right">UGX {{ number_format($avgRevenue, 0) }}</td>
                                <td class="text-right">
                                    @if($bestMonth)
                                        {{ DateTime::createFromFormat('!m', $bestMonth->month)->format('M') }}
                                        <br><small class="text-muted">UGX {{ number_format($bestMonth->total_revenue, 0) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $seasonAvg = $data['summary']['average_period_revenue'];
                                        $performance = $avgRevenue >= $seasonAvg * 1.1 ? 'success' : ($avgRevenue >= $seasonAvg * 0.9 ? 'warning' : 'danger');
                                        $icon = $avgRevenue >= $seasonAvg * 1.1 ? 'fa-arrow-up' : ($avgRevenue >= $seasonAvg * 0.9 ? 'fa-minus' : 'fa-arrow-down');
                                    @endphp
                                    <span class="badge badge-{{ $performance }}">
                                        <i class="fas {{ $icon }}"></i>
                                    </span>
                                </td>
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

<!-- Trend Analysis -->
<div class="col-md-{{ $report->type === 'monthly' && $data['period_data']->count() >= 12 ? '6' : '12' }}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> Trend Analysis
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h6>Performance Indicators:</h6>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['growth_rate'] >= 10 ? 'success' : ($data['summary']['growth_rate'] >= 0 ? 'warning' : 'danger') }}"></i>
                            Overall growth: <strong>{{ $data['summary']['growth_rate'] >= 0 ? '+' : '' }}{{ number_format($data['summary']['growth_rate'], 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['volatility_index'] <= 20 ? 'success' : ($data['summary']['volatility_index'] <= 40 ? 'warning' : 'danger') }}"></i>
                            Volatility: <strong>{{ number_format($data['summary']['volatility_index'], 1) }}%</strong>
                        </li>
                        @if($data['summary']['best_period'])
                        <li>
                            <i class="fas fa-circle text-success"></i>
                            Peak performance: <strong>UGX {{ number_format($data['summary']['best_period']->total_revenue, 0) }}</strong>
                        </li>
                        @endif
                    </ul>
                </div>
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
                <i class="fas fa-lightbulb"></i> Time-Based Analysis & Insights
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Performance Summary</h5>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['growth_rate'] >= 0 ? 'success' : 'danger' }}"></i>
                            Revenue trend: <strong>{{ $data['summary']['growth_rate'] >= 0 ? 'Growing' : 'Declining' }}</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-info"></i>
                            Consistency level: <strong>
                                {{ $data['summary']['volatility_index'] <= 20 ? 'Very Stable' : ($data['summary']['volatility_index'] <= 40 ? 'Moderate' : 'Highly Variable') }}
                            </strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-primary"></i>
                            {{ $report->type === 'monthly' ? 'Monthly' : 'Yearly' }} average: <strong>UGX {{ number_format($data['summary']['average_period_revenue'], 0) }}</strong>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Strategic Recommendations</h5>
                    <ul class="list-unstyled">
                        @if($data['summary']['growth_rate'] < 0)
                            <li><i class="fas fa-exclamation-triangle text-danger"></i> Address declining revenue trend immediately</li>
                        @elseif($data['summary']['growth_rate'] < 5)
                            <li><i class="fas fa-chart-line text-warning"></i> Implement growth strategies to improve performance</li>
                        @endif
                        
                        @if($data['summary']['volatility_index'] > 40)
                            <li><i class="fas fa-balance-scale text-warning"></i> Work on stabilizing revenue streams</li>
                        @endif
                        
                        @if($report->type === 'monthly' && $data['period_data']->count() >= 12)
                            <li><i class="fas fa-calendar text-info"></i> Leverage seasonal patterns for planning</li>
                        @endif
                        
                        @if($data['summary']['growth_rate'] >= 10 && $data['summary']['volatility_index'] <= 30)
                            <li><i class="fas fa-trophy text-success"></i> Excellent performance - maintain current strategies</li>
                        @endif
                        
                        <li><i class="fas fa-target text-primary"></i> Focus on consistency in top-performing periods</li>
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
    @if($report->format === 'chart')
        initializeTimeTrendChart();
        initializeInvoiceVolumeChart();
        initializePaymentRateChart();
    @endif
});

function initializeTimeTrendChart() {
    const ctx = document.getElementById('timeTrendChart');
    if (!ctx) return;
    
    const periodData = @json($data['period_data']->values());
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: periodData.map(period => {
                @if($report->type === 'monthly')
                    return `${period.month}/${period.year}`;
                @else
                    return period.year;
                @endif
            }),
            datasets: [{
                label: 'Total Revenue',
                data: periodData.map(period => period.total_revenue),
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Paid Revenue',
                data: periodData.map(period => period.paid_revenue),
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4
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

function initializeInvoiceVolumeChart() {
    const ctx = document.getElementById('invoiceVolumeChart');
    if (!ctx) return;
    
    const periodData = @json($data['period_data']->values());
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: periodData.map(period => {
                @if($report->type === 'monthly')
                    return `${period.month}/${period.year}`;
                @else
                    return period.year;
                @endif
            }),
            datasets: [{
                label: 'Invoice Count',
                data: periodData.map(period => period.invoice_count),
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initializePaymentRateChart() {
    const ctx = document.getElementById('paymentRateChart');
    if (!ctx) return;
    
    const periodData = @json($data['period_data']->values());
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: periodData.map(period => {
                @if($report->type === 'monthly')
                    return `${period.month}/${period.year}`;
                @else
                    return period.year;
                @endif
            }),
            datasets: [{
                label: 'Payment Rate (%)',
                data: periodData.map(period => {
                    return period.total_revenue > 0 ? (period.paid_revenue / period.total_revenue) * 100 : 0;
                }),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Payment Rate: ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection