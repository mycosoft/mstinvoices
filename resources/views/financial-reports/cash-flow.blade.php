@extends('adminlte::page')

@section('title', 'Cash Flow Statement')

@section('content_header')
    <h1>Cash Flow Statement</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('financial-reports.cash-flow') }}" class="d-flex align-items-center gap-2">
            <label class="mb-0">From:</label>
            <input type="date" name="from_date" class="form-control form-control-sm" style="max-width:160px" value="{{ $fromDate }}">
            <label class="mb-0">To:</label>
            <input type="date" name="to_date" class="form-control form-control-sm" style="max-width:160px" value="{{ $toDate }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i> Refresh</button>
        </form>
    </div>
    <div class="card-body">
        <h5 class="text-muted">Period: {{ $fromDate }} to {{ $toDate }}</h5>

        <div class="card">
            <div class="card-header bg-info text-white"><strong>Operating Activities</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tr><td>Cash Inflows (Income)</td><td class="text-right">{{ number_format($cashInflows, 2) }}</td></tr>
                    <tr><td>Cash Outflows (Expenses)</td><td class="text-right">({{ number_format($cashOutflows, 2) }})</td></tr>
                    <tr class="font-weight-bold"><td>Net Operating Cash Flow</td><td class="text-right {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netCashFlow, 2) }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white"><strong>Net Cash Flow Summary</strong></div>
            <div class="card-body">
                <table class="table mb-0">
                    <tr><td>Opening Cash Balance</td><td class="text-right">{{ number_format($openingCash, 2) }}</td></tr>
                    <tr><td>Net Cash Flow (Period)</td><td class="text-right {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netCashFlow, 2) }}</td></tr>
                    <tr class="font-weight-bold"><td>Closing Cash Balance</td><td class="text-right">{{ number_format($closingCash, 2) }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
