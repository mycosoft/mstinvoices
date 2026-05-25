@extends('adminlte::page')

@section('title', 'Balance Sheet')

@section('content_header')
    <h1>Balance Sheet</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('financial-reports.balance-sheet') }}" class="d-flex align-items-center gap-2">
            <label class="mb-0">As of:</label>
            <input type="date" name="as_of_date" class="form-control form-control-sm" style="max-width:180px" value="{{ $as_of_date }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i> Refresh</button>
            <a href="{{ route('financial-reports.export-pdf', ['report' => 'balance-sheet', 'as_of_date' => $as_of_date]) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </form>
    </div>
    <div class="card-body">
        <h5 class="text-muted">As of {{ $as_of_date }}</h5>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white"><strong>Assets</strong></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            @forelse($assets as $item)
                            <tr><td>{{ $item['code'] }} - {{ $item['name'] }}</td><td class="text-right">{{ number_format($item['balance'], 2) }}</td></tr>
                            @empty
                            <tr><td class="text-muted">No assets</td><td></td></tr>
                            @endforelse
                            <tr class="font-weight-bold bg-light"><td>Total Assets</td><td class="text-right">{{ number_format($total_assets, 2) }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning"><strong>Liabilities</strong></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            @forelse($liabilities as $item)
                            <tr><td>{{ $item['code'] }} - {{ $item['name'] }}</td><td class="text-right">{{ number_format($item['balance'], 2) }}</td></tr>
                            @empty
                            <tr><td class="text-muted">No liabilities</td><td></td></tr>
                            @endforelse
                            <tr class="font-weight-bold bg-light"><td>Total Liabilities</td><td class="text-right">{{ number_format($total_liabilities, 2) }}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-success text-white"><strong>Equity</strong></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            @forelse($equity as $item)
                            <tr><td>{{ $item['code'] }} - {{ $item['name'] }}</td><td class="text-right">{{ number_format($item['balance'], 2) }}</td></tr>
                            @empty
                            <tr><td class="text-muted">No equity</td><td></td></tr>
                            @endforelse
                            <tr><td>Net Income (Current)</td><td class="text-right">{{ number_format($net_income, 2) }}</td></tr>
                            <tr class="font-weight-bold bg-light"><td>Total Equity</td><td class="text-right">{{ number_format($total_equity + $net_income, 2) }}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-info text-white"><strong>Total Liabilities & Equity</strong></div>
                    <div class="card-body">
                        <h4 class="text-right">{{ number_format($total_liabilities_equity, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
