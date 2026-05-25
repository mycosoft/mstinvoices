@extends('adminlte::page')

@section('title', 'Income Statement')

@section('content_header')
    <h1>Income Statement</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('financial-reports.income-statement') }}" class="d-flex align-items-center gap-2">
            <label class="mb-0">From:</label>
            <input type="date" name="from_date" class="form-control form-control-sm" style="max-width:160px" value="{{ $from_date->format('Y-m-d') }}">
            <label class="mb-0">To:</label>
            <input type="date" name="to_date" class="form-control form-control-sm" style="max-width:160px" value="{{ $to_date->format('Y-m-d') }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i> Refresh</button>
            <a href="{{ route('financial-reports.export-pdf', ['report' => 'income-statement', 'from_date' => $from_date->format('Y-m-d'), 'to_date' => $to_date->format('Y-m-d')]) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </form>
    </div>
    <div class="card-body">
        <h5 class="text-muted">Period: {{ $from_date->format('Y-m-d') }} to {{ $to_date->format('Y-m-d') }}</h5>

        <h5>Revenue</h5>
        <table class="table table-sm">
            @forelse($revenue as $item)
            <tr><td>{{ $item['code'] }} - {{ $item['name'] }}</td><td class="text-right">{{ number_format($item['balance'], 2) }}</td></tr>
            @empty
            <tr><td class="text-muted">No revenue recorded</td><td></td></tr>
            @endforelse
            <tr class="font-weight-bold"><td>Total Revenue</td><td class="text-right">{{ number_format($total_revenue, 2) }}</td></tr>
        </table>

        <h5>Cost of Goods Sold</h5>
        <table class="table table-sm">
            <tr><td>Cost of Goods Sold</td><td class="text-right">{{ number_format($cogs, 2) }}</td></tr>
            <tr class="font-weight-bold"><td>Gross Profit</td><td class="text-right">{{ number_format($gross_profit, 2) }}</td></tr>
        </table>

        <h5>Expenses</h5>
        <table class="table table-sm">
            @forelse($expenses as $item)
            <tr><td>{{ $item['code'] }} - {{ $item['name'] }}</td><td class="text-right">{{ number_format($item['balance'], 2) }}</td></tr>
            @empty
            <tr><td class="text-muted">No expenses recorded</td><td></td></tr>
            @endforelse
            <tr class="font-weight-bold"><td>Total Expenses</td><td class="text-right">{{ number_format($total_expenses, 2) }}</td></tr>
        </table>

        <hr>
        <h4 class="font-weight-bold">Net {{ $net_income >= 0 ? 'Income' : 'Loss' }}:
            <span class="{{ $net_income >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($net_income, 2) }}
            </span>
        </h4>
    </div>
</div>
@stop
