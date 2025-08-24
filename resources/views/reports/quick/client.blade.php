@extends('adminlte::page')

@section('title', 'Client Performance Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-friends text-primary"></i> Client Performance Report</h1>
        <div class="btn-group">
            <a href="{{ route('reports.quick.export.pdf', ['type' => 'client', 'range' => request('range', 'this_year')]) }}" 
               class="btn btn-danger" title="Export PDF">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.quick.export.excel', ['type' => 'client', 'range' => request('range', 'this_year')]) }}" 
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
                <form method="GET" action="{{ route('reports.quick.client') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="range" class="form-control" onchange="this.form.submit()">
                                <option value="this_month" {{ request('range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ request('range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="this_quarter" {{ request('range') == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                <option value="last_quarter" {{ request('range') == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                                <option value="this_year" {{ request('range') == 'this_year' || !request('range') ? 'selected' : '' }}>This Year</option>
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
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($reportData['summary']['total_clients']) }}</h3>
                <p>Total Clients</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($reportData['summary']['active_clients']) }}</h3>
                <p>Active Clients</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    
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
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>UGX {{ number_format($reportData['summary']['average_revenue_per_client'], 0) }}</h3>
                <p>Avg per Client</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Client Performance Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table"></i> Client Performance Details</h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ count($reportData['client_data']) }} clients with activity</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($reportData['client_data']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Contact</th>
                                    <th class="text-center">Invoices</th>
                                    <th class="text-right">Total Revenue</th>
                                    <th class="text-right">Paid Revenue</th>
                                    <th class="text-right">Outstanding</th>
                                    <th class="text-right">Avg Invoice</th>
                                    <th class="text-center">Payment Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['client_data'] as $client)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $client->name }}</strong>
                                            @if($client->company_name)
                                                <br><small class="text-muted">{{ $client->company_name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($client->email)
                                            <i class="fas fa-envelope text-muted"></i> {{ $client->email }}<br>
                                        @endif
                                        @if($client->phone)
                                            <i class="fas fa-phone text-muted"></i> {{ $client->phone }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $client->invoice_count }}</span>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($client->total_revenue, 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        UGX {{ number_format($client->paid_revenue, 0) }}
                                    </td>
                                    <td class="text-right">
                                        @if($client->outstanding_amount > 0)
                                            <span class="text-warning">
                                                UGX {{ number_format($client->outstanding_amount, 0) }}
                                            </span>
                                        @else
                                            <span class="text-success">UGX 0</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        UGX {{ number_format($client->average_invoice_value, 0) }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $paymentRate = $client->total_revenue > 0 ? ($client->paid_revenue / $client->total_revenue) * 100 : 0;
                                        @endphp
                                        <span class="badge badge-{{ $paymentRate >= 80 ? 'success' : ($paymentRate >= 50 ? 'warning' : 'danger') }}">
                                            {{ number_format($paymentRate, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td class="text-center">
                                        <strong>{{ $reportData['client_data']->sum('invoice_count') }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['client_data']->sum('total_revenue'), 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['client_data']->sum('paid_revenue'), 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['client_data']->sum('outstanding_amount'), 0) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>UGX {{ number_format($reportData['client_data']->avg('average_invoice_value'), 0) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $totalRevenue = $reportData['client_data']->sum('total_revenue');
                                            $totalPaid = $reportData['client_data']->sum('paid_revenue');
                                            $overallRate = $totalRevenue > 0 ? ($totalPaid / $totalRevenue) * 100 : 0;
                                        @endphp
                                        <strong>
                                            <span class="badge badge-primary">{{ number_format($overallRate, 1) }}%</span>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Client Activity</h4>
                        <p class="text-muted">No clients have activity in the selected date range.</p>
                        <a href="{{ route('clients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Client
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    @if(count($reportData['client_data']) > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-trophy text-warning"></i> Top Revenue Generators</h3>
            </div>
            <div class="card-body">
                @foreach($reportData['client_data']->sortByDesc('total_revenue')->take(5) as $index => $client)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="badge badge-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') }}">
                            {{ $index + 1 }}
                        </span>
                        <strong>{{ $client->name }}</strong>
                        @if($client->company_name)
                            <small class="text-muted">({{ $client->company_name }})</small>
                        @endif
                    </div>
                    <span class="text-success font-weight-bold">
                        UGX {{ number_format($client->total_revenue, 0) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle text-warning"></i> Clients with Outstanding</h3>
            </div>
            <div class="card-body">
                @php
                    $clientsWithOutstanding = $reportData['client_data']->where('outstanding_amount', '>', 0)->sortByDesc('outstanding_amount');
                @endphp
                @if($clientsWithOutstanding->count() > 0)
                    @foreach($clientsWithOutstanding->take(5) as $client)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $client->name }}</strong>
                            @if($client->company_name)
                                <small class="text-muted">({{ $client->company_name }})</small>
                            @endif
                        </div>
                        <span class="text-warning font-weight-bold">
                            UGX {{ number_format($client->outstanding_amount, 0) }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <p class="text-center text-success">
                        <i class="fas fa-check-circle"></i> All clients are up to date!
                    </p>
                @endif
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
<script>
// Auto-refresh data every 10 minutes
setInterval(function() {
    location.reload();
}, 600000);

// Tooltip initialization
$('[title]').tooltip();
</script>
@stop