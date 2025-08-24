@extends('adminlte::page')

@section('title', 'Top Clients Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-trophy text-warning"></i> Top Clients Report</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-download"></i> Export
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('reports.quick.export.pdf', ['type' => 'top_clients', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank">
                    <i class="fas fa-file-pdf text-danger"></i> Export as PDF
                </a>
                <a class="dropdown-item" href="{{ route('reports.quick.export.excel', ['type' => 'top_clients', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    <i class="fas fa-file-excel text-success"></i> Export as Excel
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Date Range Filter -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Date Range Filter</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.quick.top-clients') }}" class="row">
                    <div class="col-md-4">
                        <label for="date_from">From Date:</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to">To Date:</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Update Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary elevation-1">
                <i class="fas fa-users"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Clients</span>
                <span class="info-box-number">{{ number_format($data['summary']['total_clients']) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-dollar-sign"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Revenue</span>
                <span class="info-box-number">UGX {{ number_format($data['summary']['total_revenue'], 0) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-calculator"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Avg Revenue/Client</span>
                <span class="info-box-number">UGX {{ number_format($data['summary']['avg_revenue_per_client'], 0) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-trophy"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Top Client Revenue</span>
                <span class="info-box-number">UGX {{ number_format($data['summary']['top_client_revenue'], 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Top Clients Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i> Top 10 Clients Revenue Comparison
                </h3>
            </div>
            <div class="card-body">
                <canvas id="topClientsChart" style="height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Distribution -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i> Revenue Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="revenueDistributionChart" style="height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Clients Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table"></i> Top Clients Details
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Rank</th>
                                <th>Client Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th class="text-center">Invoices</th>
                                <th class="text-right">Total Revenue</th>
                                <th class="text-right">Paid Revenue</th>
                                <th class="text-right">Avg Invoice</th>
                                <th class="text-right">Outstanding</th>
                                <th class="text-center">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['top_clients'] as $index => $client)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'dark') }}">
                                            <i class="fas fa-{{ $index === 0 ? 'trophy' : ($index === 1 ? 'medal' : 'award') }}"></i>
                                            #{{ $index + 1 }}
                                        </span>
                                    @else
                                        <span class="badge badge-light">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $client->name }}</strong>
                                </td>
                                <td>
                                    {{ $client->company_name ?? 'Individual' }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ $client->email }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ number_format($client->invoice_count) }}</span>
                                </td>
                                <td class="text-right">
                                    <strong>UGX {{ number_format($client->total_revenue, 0) }}</strong>
                                </td>
                                <td class="text-right">
                                    UGX {{ number_format($client->paid_revenue, 0) }}
                                </td>
                                <td class="text-right">
                                    UGX {{ number_format($client->avg_invoice_value, 0) }}
                                </td>
                                <td class="text-right">
                                    @if($client->outstanding_amount > 0)
                                        <span class="text-warning">
                                            UGX {{ number_format($client->outstanding_amount, 0) }}
                                        </span>
                                    @else
                                        <span class="text-success">
                                            <i class="fas fa-check"></i> Paid
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $percentage = $data['summary']['total_revenue'] > 0 ? ($client->total_revenue / $data['summary']['total_revenue']) * 100 : 0;
                                        $paymentRate = $client->total_revenue > 0 ? ($client->paid_revenue / $client->total_revenue) * 100 : 0;
                                    @endphp
                                    <div class="progress mb-1" style="height: 10px;">
                                        <div class="progress-bar bg-primary" style="width: {{ min($percentage, 100) }}%" title="Revenue Share: {{ number_format($percentage, 1) }}%"></div>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $paymentRate >= 80 ? 'success' : ($paymentRate >= 50 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $paymentRate }}%" title="Payment Rate: {{ number_format($paymentRate, 1) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.info-box {
    margin-bottom: 20px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.badge {
    font-size: 0.9em;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Top Clients Bar Chart
const topClientsCtx = document.getElementById('topClientsChart').getContext('2d');
const topClientsData = {!! json_encode($data['top_clients']->take(10)->pluck('total_revenue')->values()) !!};
const topClientsLabels = {!! json_encode($data['top_clients']->take(10)->map(function($client) { return $client->name; })->values()) !!};

new Chart(topClientsCtx, {
    type: 'bar',
    data: {
        labels: topClientsLabels,
        datasets: [{
            label: 'Total Revenue (UGX)',
            data: topClientsData,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: UGX ' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'UGX ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Revenue Distribution Pie Chart
const distributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
const top5Data = {!! json_encode($data['top_clients']->take(5)->pluck('total_revenue')->values()) !!};
const top5Labels = {!! json_encode($data['top_clients']->take(5)->map(function($client) { return $client->name; })->values()) !!};

new Chart(distributionCtx, {
    type: 'pie',
    data: {
        labels: top5Labels,
        datasets: [{
            data: top5Data,
            backgroundColor: [
                '#FF6384',
                '#36A2EB', 
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
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
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return context.label + ': UGX ' + value.toLocaleString() + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Auto-refresh when dates change
document.getElementById('date_from').addEventListener('change', function() {
    if (this.form.date_to.value) {
        this.form.submit();
    }
});

document.getElementById('date_to').addEventListener('change', function() {
    if (this.form.date_from.value) {
        this.form.submit();
    }
});
</script>
@stop