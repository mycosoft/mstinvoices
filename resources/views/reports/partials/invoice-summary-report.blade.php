<!-- Invoice Summary Report -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-invoice"></i> Invoice Summary Overview
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-file-invoice"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Invoices</span>
                            <span class="info-box-number">{{ number_format($data['summary']['total_invoices']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Paid Invoices</span>
                            <span class="info-box-number">{{ number_format($data['summary']['paid_invoices']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Invoices</span>
                            <span class="info-box-number">{{ number_format($data['summary']['pending_invoices']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Overdue Invoices</span>
                            <span class="info-box-number">{{ number_format($data['summary']['overdue_invoices']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Value</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['total_value'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-calculator"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Average Value</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['average_value'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-dark"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Payment Rate</span>
                            <span class="info-box-number">{{ number_format($data['summary']['payment_rate'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($report->format === 'chart')
<!-- Invoice Status Distribution Chart -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-pie"></i> Invoice Status Distribution
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Value Trend -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> Invoice Creation Trend
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="creationTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@if($report->format === 'table' || $report->format === 'summary')
<!-- Recent Invoices -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Invoice Details
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="text-right">Amount</th>
                            <th>Status</th>
                            <th class="text-right">Days Outstanding</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['invoices'] as $invoice)
                        <tr>
                            <td>
                                <strong>{{ $invoice->invoice_number }}</strong>
                            </td>
                            <td>
                                {{ $invoice->client_name }}
                                @if($invoice->client_company)
                                    <br><small class="text-muted">{{ $invoice->client_company }}</small>
                                @endif
                            </td>
                            <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                            <td>
                                {{ $invoice->due_date->format('M d, Y') }}
                                @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                                    <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                @endif
                            </td>
                            <td class="text-right">
                                <strong>UGX {{ number_format($invoice->total_amount, 0) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-{{ 
                                    $invoice->status === 'paid' ? 'success' : 
                                    ($invoice->status === 'pending' ? 'warning' : 
                                    ($invoice->status === 'overdue' ? 'danger' : 'secondary')) 
                                }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="text-right">
                                @php
                                    $daysOutstanding = $invoice->invoice_date->diffInDays(now());
                                    $badgeColor = $daysOutstanding <= 30 ? 'success' : ($daysOutstanding <= 60 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">
                                    {{ $daysOutstanding }} days
                                </span>
                            </td>
                            <td>
                                @if($invoice->payment_method)
                                    <span class="badge badge-info">{{ ucfirst($invoice->payment_method) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No invoices found for the selected period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Status Breakdown -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Status Breakdown
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Value</th>
                            <th class="text-right">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statuses = [
                                'paid' => ['color' => 'success', 'icon' => 'check-circle'],
                                'pending' => ['color' => 'warning', 'icon' => 'clock'],
                                'overdue' => ['color' => 'danger', 'icon' => 'exclamation-triangle'],
                                'draft' => ['color' => 'secondary', 'icon' => 'edit']
                            ];
                        @endphp
                        @foreach($statuses as $status => $config)
                            @php
                                $statusData = $data['status_breakdown'][$status] ?? ['count' => 0, 'value' => 0];
                                $percentage = $data['summary']['total_invoices'] > 0 ? 
                                    ($statusData['count'] / $data['summary']['total_invoices']) * 100 : 0;
                            @endphp
                            @if($statusData['count'] > 0)
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $config['color'] }}">
                                        <i class="fas fa-{{ $config['icon'] }}"></i> {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="text-right">{{ number_format($statusData['count']) }}</td>
                                <td class="text-right">UGX {{ number_format($statusData['value'], 0) }}</td>
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

<!-- Top Clients by Invoice Count -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i> Top Clients by Volume
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th class="text-right">Invoices</th>
                            <th class="text-right">Total Value</th>
                            <th class="text-right">Avg Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['top_clients_by_volume'] as $client)
                        <tr>
                            <td>
                                <strong>{{ $client->client_name }}</strong>
                                @if($client->client_company)
                                    <br><small class="text-muted">{{ $client->client_company }}</small>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="badge badge-primary">{{ $client->invoice_count }}</span>
                            </td>
                            <td class="text-right">UGX {{ number_format($client->total_value, 0) }}</td>
                            <td class="text-right">UGX {{ number_format($client->average_value, 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No client data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Key Performance Indicators -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lightbulb"></i> Invoice Performance Analysis
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Performance Metrics</h5>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['payment_rate'] >= 80 ? 'success' : ($data['summary']['payment_rate'] >= 60 ? 'warning' : 'danger') }}"></i>
                            Payment rate: <strong>{{ number_format($data['summary']['payment_rate'], 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-info"></i>
                            Average invoice value: <strong>UGX {{ number_format($data['summary']['average_value'], 0) }}</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['overdue_invoices'] == 0 ? 'success' : ($data['summary']['overdue_invoices'] <= 5 ? 'warning' : 'danger') }}"></i>
                            Overdue invoices: <strong>{{ number_format($data['summary']['overdue_invoices']) }}</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-primary"></i>
                            Average days to payment: <strong>{{ number_format($data['summary']['average_payment_days'] ?? 0, 0) }} days</strong>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Recommendations</h5>
                    <ul class="list-unstyled">
                        @if($data['summary']['payment_rate'] < 70)
                            <li><i class="fas fa-exclamation-triangle text-danger"></i> Review payment terms and follow-up procedures</li>
                        @endif
                        @if($data['summary']['overdue_invoices'] > 10)
                            <li><i class="fas fa-bell text-warning"></i> Implement automated overdue reminders</li>
                        @endif
                        @if(($data['summary']['average_payment_days'] ?? 0) > 45)
                            <li><i class="fas fa-clock text-danger"></i> Consider offering early payment discounts</li>
                        @endif
                        @if($data['summary']['average_value'] < 500000)
                            <li><i class="fas fa-arrow-up text-info"></i> Explore opportunities to increase invoice values</li>
                        @endif
                        @if($data['summary']['payment_rate'] >= 85 && $data['summary']['overdue_invoices'] <= 5)
                            <li><i class="fas fa-thumbs-up text-success"></i> Excellent invoice management performance!</li>
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
        initializeStatusChart();
        initializeCreationTrendChart();
    @endif
});

function initializeStatusChart() {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;
    
    const statusData = @json($data['status_breakdown']);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
            datasets: [{
                data: Object.values(statusData).map(item => item.count),
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // success - paid
                    'rgba(255, 193, 7, 0.8)',   // warning - pending
                    'rgba(220, 53, 69, 0.8)',   // danger - overdue
                    'rgba(108, 117, 125, 0.8)'  // secondary - draft
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
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
                            const status = Object.keys(statusData)[context.dataIndex];
                            const data = statusData[status];
                            return status.charAt(0).toUpperCase() + status.slice(1) + ': ' + 
                                   data.count + ' invoices (UGX ' + data.value.toLocaleString() + ')';
                        }
                    }
                }
            }
        }
    });
}

function initializeCreationTrendChart() {
    const ctx = document.getElementById('creationTrendChart');
    if (!ctx) return;
    
    const trendData = @json($data['creation_trend'] ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(item => `${item.month}/${item.year}`),
            datasets: [{
                label: 'Invoices Created',
                data: trendData.map(item => item.count),
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderColor: 'rgba(54, 162, 235, 1)',
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
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Invoices: ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection