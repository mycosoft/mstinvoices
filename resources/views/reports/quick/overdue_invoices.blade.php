@extends('adminlte::page')

@section('title', 'Overdue Invoices Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-exclamation-triangle text-danger"></i> Overdue Invoices Report</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-download"></i> Export
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('reports.quick.export.pdf', ['type' => 'overdue_invoices', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank">
                    <i class="fas fa-file-pdf text-danger"></i> Export as PDF
                </a>
                <a class="dropdown-item" href="{{ route('reports.quick.export.excel', ['type' => 'overdue_invoices', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
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
                <form method="GET" action="{{ route('reports.quick.overdue-invoices') }}" class="row">
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
            <span class="info-box-icon bg-danger elevation-1">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Overdue</span>
                <span class="info-box-number">UGX {{ number_format($data['summary']['total_overdue_amount'], 0) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-file-invoice"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Overdue Count</span>
                <span class="info-box-number">{{ number_format($data['summary']['overdue_count']) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-calendar-alt"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Avg Days Overdue</span>
                <span class="info-box-number">{{ number_format($data['summary']['average_days_overdue'], 0) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-secondary elevation-1">
                <i class="fas fa-clock"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Oldest Overdue</span>
                <span class="info-box-number">{{ number_format($data['summary']['oldest_overdue_days'], 0) }} days</span>
            </div>
        </div>
    </div>

    <!-- Age Groups Summary -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list-alt"></i> Age Groups Breakdown
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($data['age_groups'] as $group => $amount)
                    <div class="col-6 mb-3">
                        <div class="description-block border-right">
                            <span class="description-percentage text-{{ $group === '90+' ? 'danger' : ($group === '61-90' ? 'warning' : 'info') }}">
                                <i class="fas fa-clock"></i>
                            </span>
                            <h5 class="description-header">UGX {{ number_format($amount, 0) }}</h5>
                            <span class="description-text">{{ $group }} DAYS</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Invoices Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table"></i> Overdue Invoices Details
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th class="text-right">Amount Due</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['overdue_invoices'] as $invoice)
                            <tr>
                                <td>
                                    <strong>{{ $invoice->invoice_number }}</strong>
                                </td>
                                <td>
                                    {{ $invoice->client->name }}<br>
                                    <small class="text-muted">{{ $invoice->client->company_name ?? 'Individual' }}</small>
                                </td>
                                <td>
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </td>
                                <td>
                                    @php
                                        $daysOverdue = now()->diffInDays($invoice->due_date);
                                        $badgeColor = $daysOverdue > 90 ? 'danger' : ($daysOverdue > 30 ? 'warning' : 'info');
                                    @endphp
                                    <span class="badge badge-{{ $badgeColor }}">
                                        {{ $daysOverdue }} days
                                    </span>
                                </td>
                                <td class="text-right">
                                    <strong class="text-danger">
                                        UGX {{ number_format($invoice->balance_due, 0) }}
                                    </strong>
                                </td>
                                <td>
                                    @if($invoice->payment_status === 'unpaid')
                                        <span class="badge badge-danger">Unpaid</span>
                                    @elseif($invoice->payment_status === 'partial')
                                        <span class="badge badge-warning">Partial</span>
                                    @else
                                        <span class="badge badge-success">Paid</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-info btn-sm" title="View Invoice">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($invoice->payment_status !== 'paid')
                                        <button type="button" class="btn btn-success btn-sm" title="Record Payment" 
                                                onclick="recordPayment({{ $invoice->id }}, {{ $invoice->balance_due }})">
                                            <i class="fas fa-dollar-sign"></i>
                                        </button>
                                        @endif
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

.description-block {
    margin: 0;
    padding: 15px;
    text-align: center;
}

.description-header {
    font-size: 1.2em;
    margin: 10px 0 5px 0;
    font-weight: bold;
}

.description-text {
    font-size: 0.75em;
    text-transform: uppercase;
    font-weight: 600;
    color: #6c757d;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Record Payment Function
function recordPayment(invoiceId, amount) {
    if (confirm('Record payment for this invoice?')) {
        // This would typically open a modal or redirect to payment form
        window.location.href = `/invoices/${invoiceId}#payments`;
    }
}

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