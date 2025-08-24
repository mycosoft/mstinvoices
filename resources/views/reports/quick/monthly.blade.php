@extends('adminlte::page')

@section('title', 'Monthly Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-alt text-info"></i> Monthly Report - {{ $year }}</h1>
        <div class="btn-group">
            <a href="{{ route('reports.quick.export.pdf', ['type' => 'monthly', 'year' => $year]) }}" 
               class="btn btn-danger" title="Export PDF">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.quick.export.excel', ['type' => 'monthly', 'year' => $year]) }}" 
               class="btn btn-success" title="Export Excel">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Year Filter -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar"></i> Select Year</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.quick.monthly') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="year" class="form-control" onchange="this.form.submit()">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0 text-muted">
                                <i class="fas fa-info-circle"></i>
                                Showing monthly breakdown for {{ $year }}
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>UGX {{ number_format($reportData['summary']['total_revenue'], 0) }}</h3>
                <p>Total Revenue {{ $year }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($reportData['summary']['total_invoices']) }}</h3>
                <p>Total Invoices</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>UGX {{ number_format($reportData['summary']['average_monthly_revenue'], 0) }}</h3>
                <p>Avg Monthly Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ count($reportData['monthly_data']) }}</h3>
                <p>Active Months</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Monthly Performance Chart -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Monthly Performance Chart</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Details Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table"></i> Monthly Breakdown</h3>
            </div>
            <div class="card-body p-0">
                @if(count($reportData['monthly_data']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-center">Invoices</th>
                                    <th class="text-right">Total Revenue</th>
                                    <th class="text-right">Paid Revenue</th>
                                    <th class="text-right">Average Invoice</th>
                                    <th class="text-center">Growth</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $previousRevenue = 0; @endphp
                                @foreach($reportData['monthly_data'] as $index => $data)
                                <tr>
                                    <td>
                                        <strong>{{ DateTime::createFromFormat('!m', $data->month)->format('F') }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $data->invoice_count }}</span>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($data->total_revenue, 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        UGX {{ number_format($data->paid_revenue, 0) }}
                                    </td>
                                    <td class="text-right">
                                        UGX {{ number_format($data->average_invoice_value, 0) }}
                                    </td>
                                    <td class="text-center">
                                        @if($index > 0 && $previousRevenue > 0)
                                            @php
                                                $growth = (($data->total_revenue - $previousRevenue) / $previousRevenue) * 100;
                                            @endphp
                                            <span class="badge badge-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                        @php $previousRevenue = $data->total_revenue; @endphp
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td class="text-center">
                                        <strong>{{ $reportData['monthly_data']->sum('invoice_count') }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['monthly_data']->sum('total_revenue'), 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['monthly_data']->sum('paid_revenue'), 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['monthly_data']->avg('average_invoice_value'), 0) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $firstMonth = $reportData['monthly_data']->first();
                                            $lastMonth = $reportData['monthly_data']->last();
                                            $yearGrowth = $firstMonth && $lastMonth && $firstMonth->total_revenue > 0 
                                                ? (($lastMonth->total_revenue - $firstMonth->total_revenue) / $firstMonth->total_revenue) * 100 
                                                : 0;
                                        @endphp
                                        <strong>
                                            <span class="badge badge-{{ $yearGrowth >= 0 ? 'success' : 'danger' }}">
                                                Year: {{ $yearGrowth >= 0 ? '+' : '' }}{{ number_format($yearGrowth, 1) }}%
                                            </span>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Data for {{ $year }}</h4>
                        <p class="text-muted">No invoices were created in {{ $year }}.</p>
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Invoice
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    @if(count($reportData['monthly_data']) > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Best Performing Months</h3>
            </div>
            <div class="card-body">
                @foreach($reportData['monthly_data']->sortByDesc('total_revenue')->take(3) as $index => $month)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="badge badge-{{ $index == 0 ? 'success' : ($index == 1 ? 'info' : 'secondary') }}">
                            {{ $index + 1 }}
                        </span>
                        <strong>{{ DateTime::createFromFormat('!m', $month->month)->format('F') }}</strong>
                        <small class="text-muted">({{ $month->invoice_count }} invoices)</small>
                    </div>
                    <span class="text-success font-weight-bold">
                        UGX {{ number_format($month->total_revenue, 0) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Performance Insights</h3>
            </div>
            <div class="card-body">
                @php
                    $bestMonth = $reportData['monthly_data']->sortByDesc('total_revenue')->first();
                    $worstMonth = $reportData['monthly_data']->sortBy('total_revenue')->first();
                    $totalRevenue = $reportData['monthly_data']->sum('total_revenue');
                @endphp
                
                @if($bestMonth)
                <div class="mb-3">
                    <small class="text-muted">Best Month:</small><br>
                    <strong class="text-success">
                        {{ DateTime::createFromFormat('!m', $bestMonth->month)->format('F') }} 
                        - UGX {{ number_format($bestMonth->total_revenue, 0) }}
                    </strong>
                </div>
                @endif
                
                @if($worstMonth && $worstMonth->id != $bestMonth->id)
                <div class="mb-3">
                    <small class="text-muted">Lowest Month:</small><br>
                    <strong class="text-warning">
                        {{ DateTime::createFromFormat('!m', $worstMonth->month)->format('F') }} 
                        - UGX {{ number_format($worstMonth->total_revenue, 0) }}
                    </strong>
                </div>
                @endif
                
                <div class="mb-3">
                    <small class="text-muted">Average Monthly Revenue:</small><br>
                    <strong class="text-info">
                        UGX {{ number_format($reportData['summary']['average_monthly_revenue'], 0) }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@stop

@section('css')
<style>
.small-box {
    margin-bottom: 20px;
}
.table th, .table td {
    vertical-align: middle;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Revenue Chart
    @if(count($reportData['monthly_data']) > 0)
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($reportData['monthly_data'] as $data)
                    '{{ DateTime::createFromFormat("!m", $data->month)->format("M") }}',
                @endforeach
            ],
            datasets: [
                {
                    label: 'Total Revenue',
                    data: [
                        @foreach($reportData['monthly_data'] as $data)
                            {{ $data->total_revenue }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Paid Revenue',
                    data: [
                        @foreach($reportData['monthly_data'] as $data)
                            {{ $data->paid_revenue }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
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
    @endif
});
</script>
@stop