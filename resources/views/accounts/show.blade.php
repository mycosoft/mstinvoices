@extends('adminlte::page')

@section('title', $account->code . ' - ' . $account->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><a href="{{ route('accounts.index') }}">Accounts</a> / {{ $account->code }} - {{ $account->name }}</h1>
        <div>
            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><strong>Account Details</strong></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td>Code</td><td><code>{{ $account->code }}</code></td></tr>
                    <tr><td>Name</td><td>{{ $account->name }}</td></tr>
                    <tr><td>Type</td><td><span class="badge badge-info">{{ ucfirst($account->type) }}</span></td></tr>
                    <tr><td>Subtype</td><td>{{ $account->subtype ?: '-' }}</td></tr>
                    <tr><td>Normal Balance</td><td>{{ ucfirst($account->normal_balance) }}</td></tr>
                    <tr><td>Status</td><td><span class="badge badge-success">{{ ucfirst($account->status) }}</span></td></tr>
                    <tr><td>Description</td><td>{{ $account->description ?: '-' }}</td></tr>
                    @if($account->parent)
                        <tr><td>Parent</td><td>{{ $account->parent->code }} - {{ $account->parent->name }}</td></tr>
                    @endif
                </table>
                <hr>
                <div class="text-center">
                    <h4>Current Balance</h4>
                    <h2 class="text-{{ $account->current_balance >= 0 ? 'success' : 'danger' }}">
                        {{ number_format(abs($account->current_balance), 2) }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><strong>Recent Transactions</strong></div>
            <div class="card-body p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($account->journalEntryLines as $line)
                            <tr>
                                <td>{{ $line->journalEntry->date->format('Y-m-d') }}</td>
                                <td><a href="{{ route('journal-entries.show', $line->journalEntry) }}">{{ $line->journalEntry->reference_number }}</a></td>
                                <td>{{ $line->journalEntry->description }}</td>
                                <td class="text-right">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                                <td class="text-right">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted p-3">No transactions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
