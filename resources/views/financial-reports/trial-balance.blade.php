@extends('adminlte::page')

@section('title', 'Trial Balance')

@section('content_header')
    <h1>Trial Balance</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('financial-reports.trial-balance') }}" class="d-flex align-items-center gap-2">
            <label class="mb-0">As of:</label>
            <input type="date" name="as_of_date" class="form-control form-control-sm" style="max-width:180px" value="{{ $as_of_date }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i> Refresh</button>
            <a href="{{ route('financial-reports.export-pdf', ['report' => 'trial-balance', 'as_of_date' => $as_of_date]) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                <tr>
                    <td>{{ $account['code'] }}</td>
                    <td>{{ $account['name'] }}</td>
                    <td>{{ ucfirst($account['type']) }}</td>
                    <td class="text-right">{{ $account['debit'] > 0 ? number_format($account['debit'], 2) : '-' }}</td>
                    <td class="text-right">{{ $account['credit'] > 0 ? number_format($account['credit'], 2) : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4">No accounts found with balances.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="3" class="text-right">Totals</td>
                    <td class="text-right">{{ number_format($total_debit, 2) }}</td>
                    <td class="text-right">{{ number_format($total_credit, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@stop
