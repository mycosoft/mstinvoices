<!-- Payment Summary Report -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-credit-card"></i> Payment Summary Overview
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Payments</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['total_payments'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-receipt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Payment Count</span>
                            <span class="info-box-number">{{ number_format($data['summary']['payment_count']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-calculator"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Average Payment</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['average_payment'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Payment Days</span>
                            <span class="info-box-number">{{ number_format($data['summary']['average_payment_days'], 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-day"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Early Payments</span>
                            <span class="info-box-number">{{ number_format($data['summary']['early_payments']) }}</span>
                            <div class="info-box-more">{{ number_format($data['summary']['early_payment_rate'], 1) }}% of total</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-dark"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">On-Time Payments</span>
                            <span class="info-box-number">{{ number_format($data['summary']['ontime_payments']) }}</span>
                            <div class="info-box-more">{{ number_format($data['summary']['ontime_payment_rate'], 1) }}% of total</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-calendar-times"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Late Payments</span>
                            <span class="info-box-number">{{ number_format($data['summary']['late_payments']) }}</span>
                            <div class="info-box-more">{{ number_format($data['summary']['late_payment_rate'], 1) }}% of total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($report->format === 'chart')
<!-- Payment Method Distribution -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-pie"></i> Payment Methods
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Payment Timing Analysis -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Payment Timing
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="paymentTimingChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Payment Trend -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> Monthly Payment Trend
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="paymentTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@if($report->format === 'table' || $report->format === 'summary')
<!-- Recent Payments -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Payment Details
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th>Invoice #</th>
                            <th>Client</th>
                            <th class="text-right">Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-right">Days to Pay</th>
                            <th>Timing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['payments'] as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td>
                                <strong>{{ $payment->invoice_number }}</strong>
                            </td>
                            <td>
                                {{ $payment->client_name }}
                                @if($payment->client_company)
                                    <br><small class="text-muted">{{ $payment->client_company }}</small>
                                @endif
                            </td>
                            <td class="text-right">
                                <strong>UGX {{ number_format($payment->amount, 0) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-{{ 
                                    $payment->payment_method === 'cash' ? 'success' : 
                                    ($payment->payment_method === 'bank_transfer' ? 'primary' : 
                                    ($payment->payment_method === 'mobile_money' ? 'info' : 'secondary')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td>
                                @if($payment->reference_number)
                                    <code>{{ $payment->reference_number }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @php
                                    $daysToPay = $payment->invoice_date->diffInDays($payment->payment_date);
                                    $badgeColor = $daysToPay <= 15 ? 'success' : ($daysToPay <= 30 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">
                                    {{ $daysToPay }} days
                                </span>
                            </td>
                            <td>
                                @php
                                    $timing = 'on-time';
                                    $timingColor = 'success';
                                    $timingIcon = 'check';
                                    
                                    if ($payment->payment_date < $payment->due_date) {
                                        $timing = 'early';
                                        $timingColor = 'info';
                                        $timingIcon = 'clock';
                                    } elseif ($payment->payment_date > $payment->due_date) {
                                        $timing = 'late';
                                        $timingColor = 'danger';
                                        $timingIcon = 'exclamation-triangle';
                                    }
                                @endphp
                                <span class="badge badge-{{ $timingColor }}">
                                    <i class="fas fa-{{ $timingIcon }}"></i> {{ ucfirst($timing) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No payments found for the selected period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Method Analysis -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-credit-card"></i> Payment Method Breakdown
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Avg Days</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['payment_methods'] as $method => $methodData)
                        <tr>
                            <td>
                                <span class="badge badge-{{ 
                                    $method === 'cash' ? 'success' : 
                                    ($method === 'bank_transfer' ? 'primary' : 
                                    ($method === 'mobile_money' ? 'info' : 
                                    ($method === 'credit_card' ? 'warning' : 'secondary'))) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $method)) }}
                                </span>
                            </td>
                            <td class="text-right">{{ number_format($methodData['count']) }}</td>
                            <td class="text-right">UGX {{ number_format($methodData['amount'], 0) }}</td>
                            <td class="text-right">{{ number_format($methodData['avg_days'], 1) }}</td>
                            <td class="text-right">
                                {{ number_format($methodData['percentage'], 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Top Paying Clients -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i> Top Paying Clients
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th class="text-right">Payments</th>
                            <th class="text-right">Total Paid</th>
                            <th class="text-right">Avg Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['top_paying_clients'] as $client)
                        <tr>
                            <td>
                                <strong>{{ $client->client_name }}</strong>
                                @if($client->client_company)
                                    <br><small class="text-muted">{{ $client->client_company }}</small>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="badge badge-primary">{{ $client->payment_count }}</span>
                            </td>
                            <td class="text-right">UGX {{ number_format($client->total_paid, 0) }}</td>
                            <td class="text-right">
                                <span class="badge badge-{{ $client->avg_payment_days <= 30 ? 'success' : ($client->avg_payment_days <= 60 ? 'warning' : 'danger') }}">
                                    {{ number_format($client->avg_payment_days, 0) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No payment data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Payment Performance Analysis -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lightbulb"></i> Payment Performance Analysis
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Payment Efficiency Metrics</h5>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['ontime_payment_rate'] >= 80 ? 'success' : ($data['summary']['ontime_payment_rate'] >= 60 ? 'warning' : 'danger') }}"></i>
                            On-time payment rate: <strong>{{ number_format($data['summary']['ontime_payment_rate'], 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['early_payment_rate'] >= 20 ? 'success' : ($data['summary']['early_payment_rate'] >= 10 ? 'info' : 'secondary') }}"></i>
                            Early payment rate: <strong>{{ number_format($data['summary']['early_payment_rate'], 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['average_payment_days'] <= 30 ? 'success' : ($data['summary']['average_payment_days'] <= 45 ? 'warning' : 'danger') }}"></i>
                            Average payment time: <strong>{{ number_format($data['summary']['average_payment_days'], 0) }} days</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-info"></i>
                            Payment frequency: <strong>{{ number_format($data['summary']['payment_count']) }} payments</strong>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Strategic Recommendations</h5>
                    <ul class="list-unstyled">
                        @if($data['summary']['late_payment_rate'] > 20)
                            <li><i class="fas fa-exclamation-triangle text-danger"></i> High late payment rate - review credit terms</li>
                        @endif
                        @if($data['summary']['average_payment_days'] > 45)
                            <li><i class="fas fa-clock text-warning"></i> Consider early payment incentives</li>
                        @endif
                        @if($data['summary']['early_payment_rate'] < 10)
                            <li><i class="fas fa-gift text-info"></i> Implement early payment discounts</li>
                        @endif
                        @if(isset($data['payment_methods']['cash']) && $data['payment_methods']['cash']['percentage'] > 50)
                            <li><i class="fas fa-mobile-alt text-primary"></i> Promote digital payment methods</li>
                        @endif
                        @if($data['summary']['ontime_payment_rate'] >= 80 && $data['summary']['average_payment_days'] <= 30)
                            <li><i class="fas fa-thumbs-up text-success"></i> Excellent payment collection performance!</li>
                        @endif
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
        initializePaymentMethodChart();
        initializePaymentTimingChart();
        initializePaymentTrendChart();
    @endif
});

function initializePaymentMethodChart() {
    const ctx = document.getElementById('paymentMethodChart');
    if (!ctx) return;
    
    const methodData = @json($data['payment_methods']);
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.keys(methodData).map(method => method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                data: Object.values(methodData).map(item => item.amount),
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // success - cash
                    'rgba(0, 123, 255, 0.8)',   // primary - bank_transfer
                    'rgba(23, 162, 184, 0.8)',  // info - mobile_money
                    'rgba(255, 193, 7, 0.8)',   // warning - credit_card
                    'rgba(108, 117, 125, 0.8)'  // secondary - other
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const method = Object.keys(methodData)[context.dataIndex];
                            const data = methodData[method];
                            return method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) + ': UGX ' + data.amount.toLocaleString() + 
                                   ' (' + data.count + ' payments)';
                        }
                    }
                }
            }
        }
    });
}

function initializePaymentTimingChart() {
    const ctx = document.getElementById('paymentTimingChart');
    if (!ctx) return;
    
    const timingData = {
        'Early': {{ $data['summary']['early_payments'] }},
        'On Time': {{ $data['summary']['ontime_payments'] }},
        'Late': {{ $data['summary']['late_payments'] }}
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(timingData),
            datasets: [{
                label: 'Payment Count',
                data: Object.values(timingData),
                backgroundColor: [
                    'rgba(23, 162, 184, 0.8)',  // info - early
                    'rgba(40, 167, 69, 0.8)',   // success - on time
                    'rgba(220, 53, 69, 0.8)'    // danger - late
                ],
                borderColor: [
                    'rgba(23, 162, 184, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
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

function initializePaymentTrendChart() {
    const ctx = document.getElementById('paymentTrendChart');
    if (!ctx) return;
    
    const trendData = @json($data['monthly_payments'] ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(item => `${item.month}/${item.year}`),
            datasets: [{
                label: 'Payment Amount',
                data: trendData.map(item => item.amount),
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Payment Count',
                data: trendData.map(item => item.count),
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Amount: UGX ' + context.parsed.y.toLocaleString();
                            } else {
                                return 'Count: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection