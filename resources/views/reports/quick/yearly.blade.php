@extends('adminlte::page')

@section('title', 'Yearly Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar text-primary"></i> Yearly Report</h1>
        <div class="btn-group">
            <a href="{{ route('reports.quick.export.pdf', ['type' => 'yearly', 'years' => $years]) }}" 
               class="btn btn-danger" title="Export PDF">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.quick.export.excel', ['type' => 'yearly', 'years' => $years]) }}" 
               class="btn btn-success" title="Export Excel">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Year Range Filter -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar"></i> Years to Include</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.quick.yearly') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="years" class="form-control" onchange="this.form.submit()">
                                <option value="3" {{ $years == 3 ? 'selected' : '' }}>Last 3 Years</option>
                                <option value="5" {{ $years == 5 ? 'selected' : '' }}>Last 5 Years</option>
                                <option value="7" {{ $years == 7 ? 'selected' : '' }}>Last 7 Years</option>
                                <option value="10" {{ $years == 10 ? 'selected' : '' }}>Last 10 Years</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0 text-muted">
                                <i class="fas fa-info-circle"></i>
                                Showing yearly data for {{ $reportData['summary']['years_range'] }}
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
                <p>Total Revenue</p>
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
                <h3>UGX {{ number_format($reportData['summary']['average_yearly_revenue'], 0) }}</h3>
                <p>Avg Yearly Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ count($reportData['yearly_data']) }}</h3>
                <p>Active Years</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Yearly Performance Chart -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-area"></i> Yearly Revenue Trend</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="yearlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Yearly Details Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table"></i> Yearly Performance Details</h3>
            </div>
            <div class="card-body p-0">
                @if(count($reportData['yearly_data']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th class="text-center">Invoices</th>
                                    <th class="text-right">Total Revenue</th>
                                    <th class="text-right">Paid Revenue</th>
                                    <th class="text-right">Average Invoice</th>
                                    <th class="text-center">YoY Growth</th>
                                    <th class="text-center">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $previousRevenue = 0; @endphp
                                @foreach($reportData['yearly_data'] as $index => $data)
                                <tr>
                                    <td>
                                        <strong>{{ $data->year }}</strong>
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
                                    <td class="text-center">
                                        @php
                                            $paymentRate = $data->total_revenue > 0 ? ($data->paid_revenue / $data->total_revenue) * 100 : 0;
                                        @endphp
                                        <span class="badge badge-{{ $paymentRate >= 80 ? 'success' : ($paymentRate >= 60 ? 'warning' : 'danger') }}">
                                            {{ number_format($paymentRate, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td class="text-center">
                                        <strong>{{ $reportData['yearly_data']->sum('invoice_count') }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['yearly_data']->sum('total_revenue'), 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['yearly_data']->sum('paid_revenue'), 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['yearly_data']->avg('average_invoice_value'), 0) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $firstYear = $reportData['yearly_data']->first();
                                            $lastYear = $reportData['yearly_data']->last();
                                            $totalGrowth = $firstYear && $lastYear && $firstYear->total_revenue > 0 
                                                ? (($lastYear->total_revenue - $firstYear->total_revenue) / $firstYear->total_revenue) * 100 
                                                : 0;
                                        @endphp
                                        <strong>
                                            <span class="badge badge-{{ $totalGrowth >= 0 ? 'success' : 'danger' }}">
                                                {{ $totalGrowth >= 0 ? '+' : '' }}{{ number_format($totalGrowth, 1) }}%
                                            </span>
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $overallPaymentRate = $reportData['yearly_data']->sum('total_revenue') > 0 
                                                ? ($reportData['yearly_data']->sum('paid_revenue') / $reportData['yearly_data']->sum('total_revenue')) * 100 
                                                : 0;
                                        @endphp
                                        <strong>
                                            <span class="badge badge-primary">{{ number_format($overallPaymentRate, 1) }}%</span>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Historical Data</h4>
                        <p class="text-muted">No invoice data available for the selected year range.</p>
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Invoice
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Insights and Analysis -->
    @if(count($reportData['yearly_data']) > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-trophy text-warning"></i> Best Performing Years</h3>
            </div>
            <div class="card-body">
                @foreach($reportData['yearly_data']->sortByDesc('total_revenue')->take(3) as $index => $year)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="badge badge-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') }}">
                            {{ $index + 1 }}
                        </span>
                        <strong>{{ $year->year }}</strong>
                        <small class="text-muted">({{ $year->invoice_count }} invoices)</small>
                    </div>
                    <span class="text-success font-weight-bold">
                        UGX {{ number_format($year->total_revenue, 0) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Growth Analysis</h3>
            </div>
            <div class="card-body">
                @php
                    $bestYear = $reportData['yearly_data']->sortByDesc('total_revenue')->first();
                    $worstYear = $reportData['yearly_data']->sortBy('total_revenue')->first();
                    $recentYear = $reportData['yearly_data']->sortByDesc('year')->first();
                    $firstYear = $reportData['yearly_data']->sortBy('year')->first();
                    
                    $cagr = 0;
                    if ($firstYear && $recentYear && $firstYear->year != $recentYear->year && $firstYear->total_revenue > 0) {
                        $years_diff = $recentYear->year - $firstYear->year;
                        if ($years_diff > 0) {
                            $cagr = (pow(($recentYear->total_revenue / $firstYear->total_revenue), (1 / $years_diff)) - 1) * 100;
                        }
                    }
                @endphp
                
                @if($bestYear)
                <div class="mb-3">
                    <small class="text-muted">Best Year:</small><br>
                    <strong class="text-success">
                        {{ $bestYear->year }} - UGX {{ number_format($bestYear->total_revenue, 0) }}
                    </strong>
                </div>
                @endif
                
                @if($cagr != 0)
                <div class="mb-3">
                    <small class="text-muted">Compound Annual Growth Rate (CAGR):</small><br>
                    <strong class="text-{{ $cagr >= 0 ? 'success' : 'danger' }}">
                        {{ $cagr >= 0 ? '+' : '' }}{{ number_format($cagr, 1) }}% per year
                    </strong>
                </div>
                @endif
                
                @if($recentYear)
                <div class="mb-3">
                    <small class="text-muted">Most Recent Year Performance:</small><br>
                    <strong class="text-info">
                        {{ $recentYear->year }}: UGX {{ number_format($recentYear->total_revenue, 0) }}
                    </strong>
                </div>
                @endif
                
                <div class="mb-3">
                    <small class="text-muted">Average Annual Revenue:</small><br>
                    <strong class="text-primary">
                        UGX {{ number_format($reportData['summary']['average_yearly_revenue'], 0) }}
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
    // Yearly Revenue Chart
    @if(count($reportData['yearly_data']) > 0)
    const ctx = document.getElementById('yearlyChart').getContext('2d');
    const yearlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach($reportData['yearly_data'] as $data)
                    '{{ $data->year }}',
                @endforeach
            ],
            datasets: [
                {
                    label: 'Total Revenue',
                    data: [
                        @foreach($reportData['yearly_data'] as $data)
                            {{ $data->total_revenue }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Paid Revenue',
                    data: [
                        @foreach($reportData['yearly_data'] as $data)
                            {{ $data->paid_revenue }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
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