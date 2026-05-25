@extends('adminlte::page')

@section('title', 'General Ledger')

@section('content_header')
    <h1>General Ledger</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('financial-reports.general-ledger') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <select name="account_id" class="form-control form-control-sm" style="max-width:300px">
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                @endforeach
            </select>
            <label class="mb-0">From:</label>
            <input type="date" name="from_date" class="form-control form-control-sm" style="max-width:150px" value="{{ $fromDate }}">
            <label class="mb-0">To:</label>
            <input type="date" name="to_date" class="form-control form-control-sm" style="max-width:150px" value="{{ $toDate }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> View</button>
        </form>
    </div>
    @if($selectedAccount)
    <div class="card-body">
        <h5>{{ $selectedAccount->code }} - {{ $selectedAccount->name }}
            <small class="text-muted">({{ ucfirst($selectedAccount->type) }})</small>
        </h5>
        <p>Opening Balance: {{ number_format($openingBalance, 2) }}</p>
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Description</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry['date']->format('Y-m-d') }}</td>
                    <td>{{ $entry['reference'] }}</td>
                    <td>{{ $entry['description'] }}</td>
                    <td class="text-right">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}</td>
                    <td class="text-right">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}</td>
                    <td class="text-right">{{ number_format($entry['balance'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-3">No transactions for this period.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="5" class="text-right">Closing Balance</td>
                    <td class="text-right">{{ number_format($closingBalance, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="card-body">
        <div class="text-center py-4 text-muted">
            <p>Select an account above to view its ledger.</p>
        </div>
    </div>
    @endif
</div>
@stop
