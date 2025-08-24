@extends('adminlte::page')

@section('title', 'Revenue Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-dollar-sign text-success"></i> Revenue Report</h1>
        <div class="btn-group">
            <a href="{{ route('reports.quick.export.pdf', ['type' => 'revenue', 'range' => request('range', 'this_month')]) }}" 
               class="btn btn-danger" title="Export PDF">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.quick.export.excel', ['type' => 'revenue', 'range' => request('range', 'this_month')]) }}" 
               class="btn btn-success" title="Export Excel">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Date Range Filter -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar"></i> Date Range</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.quick.revenue') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="range" class="form-control" onchange="this.form.submit()">
                                <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ request('range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="this_week" {{ request('range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="last_week" {{ request('range') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                <option value="this_month" {{ request('range') == 'this_month' || !request('range') ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ request('range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="this_quarter" {{ request('range') == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                <option value="last_quarter" {{ request('range') == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                                <option value="this_year" {{ request('range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                <option value="last_year" {{ request('range') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0 text-muted">
                                <i class="fas fa-info-circle"></i>
                                Showing data from {{ $dateRange[0]->format('M d, Y') }} to {{ $dateRange[1]->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
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
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>UGX {{ number_format($reportData['summary']['paid_revenue'], 0) }}</h3>
                <p>Paid Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>UGX {{ number_format($reportData['summary']['pending_revenue'], 0) }}</h3>
                <p>Pending Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($reportData['summary']['invoice_count']) }}</h3>
                <p>Total Invoices</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie"></i> Revenue Breakdown</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-6">Average Invoice Value:</dt>
                    <dd class="col-sm-6">UGX {{ number_format($reportData['summary']['average_invoice_value'], 0) }}</dd>
                    
                    <dt class="col-sm-6">Payment Rate:</dt>
                    <dd class="col-sm-6">{{ number_format($reportData['summary']['payment_rate'], 1) }}%</dd>
                    
                    <dt class="col-sm-6">Outstanding Amount:</dt>
                    <dd class="col-sm-6 text-warning">UGX {{ number_format($reportData['summary']['pending_revenue'], 0) }}</dd>
                </dl>
                
                <!-- Payment Progress Bar -->
                <div class="progress mb-2">
                    <div class="progress-bar bg-success" style="width: {{ $reportData['summary']['payment_rate'] }}%"></div>
                </div>
                <small class="text-muted">{{ number_format($reportData['summary']['payment_rate'], 1) }}% of total revenue has been collected</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Quick Stats</h3>
            </div>
            <div class="card-body">
                @if($reportData['summary']['total_revenue'] > 0)
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success">
                                    {{ number_format(($reportData['summary']['paid_revenue'] / $reportData['summary']['total_revenue']) * 100, 1) }}%
                                </span>
                                <h5 class="description-header">UGX {{ number_format($reportData['summary']['paid_revenue'], 0) }}</h5>
                                <span class="description-text">COLLECTED</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-warning">
                                    {{ number_format(($reportData['summary']['pending_revenue'] / $reportData['summary']['total_revenue']) * 100, 1) }}%
                                </span>
                                <h5 class="description-header">UGX {{ number_format($reportData['summary']['pending_revenue'], 0) }}</h5>
                                <span class="description-text">PENDING</span>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-center text-muted">No revenue data available for the selected period.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    @if(count($reportData['monthly_data']) > 0)
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Monthly Breakdown</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Year</th>
                                <th class="text-right">Invoices</th>
                                <th class="text-right">Total Revenue</th>
                                <th class="text-right">Paid Revenue</th>
                                <th class="text-right">Payment Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['monthly_data'] as $data)
                            <tr>
                                <td>{{ DateTime::createFromFormat('!m', $data->month)->format('F') }}</td>
                                <td>{{ $data->year }}</td>
                                <td class="text-right">{{ number_format($data->invoice_count) }}</td>
                                <td class="text-right">UGX {{ number_format($data->total_revenue, 0) }}</td>
                                <td class="text-right">UGX {{ number_format($data->paid_revenue, 0) }}</td>
                                <td class="text-right">
                                    @php
                                        $rate = $data->total_revenue > 0 ? ($data->paid_revenue / $data->total_revenue) * 100 : 0;
                                    @endphp
                                    <span class="badge badge-{{ $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($rate, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light font-weight-bold">
                                <td colspan="2">Total</td>
                                <td class="text-right">{{ number_format($reportData['summary']['invoice_count']) }}</td>
                                <td class="text-right">UGX {{ number_format($reportData['summary']['total_revenue'], 0) }}</td>
                                <td class="text-right">UGX {{ number_format($reportData['summary']['paid_revenue'], 0) }}</td>
                                <td class="text-right">
                                    <span class="badge badge-primary">
                                        {{ number_format($reportData['summary']['payment_rate'], 1) }}%
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
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
.description-block {
    margin: 0;
}
.table th, .table td {
    vertical-align: middle;
}
</style>
@stop

@section('js')
<script>
// Auto-refresh data every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);

// Tooltip initialization
$('[title]').tooltip();
</script>
@stop