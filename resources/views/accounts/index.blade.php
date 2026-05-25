@extends('adminlte::page')

@section('title', 'Chart of Accounts')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Chart of Accounts</h1>
        <div>
            @if($accounts->isEmpty())
                <a href="{{ route('accounts.initialize') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-magic"></i> Initialize Default Accounts
                </a>
            @endif
            <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Account
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('accounts.index') }}" class="d-flex gap-2">
            <select name="type" class="form-control form-control-sm" style="max-width:200px">
                <option value="">All Types</option>
                @foreach($accountTypes as $value => $label)
                    <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        @forelse(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'income' => 'Income', 'expense' => 'Expenses'] as $type => $label)
            @isset($groupedAccounts[$type])
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th colspan="5" class="bg-secondary text-white">
                            <strong>{{ $label }}</strong>
                        </th>
                    </tr>
                    <tr>
                        <th style="width:120px">Code</th>
                        <th>Account Name</th>
                        <th style="width:100px">Normal Balance</th>
                        <th style="width:100px">Status</th>
                        <th class="text-right" style="width:180px">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedAccounts[$type] as $account)
                    <tr>
                        <td><code>{{ $account->code }}</code></td>
                        <td>
                            <a href="{{ route('accounts.show', $account) }}">{{ $account->name }}</a>
                            @if($account->is_system)
                                <span class="badge badge-info ml-1">System</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $account->normal_balance === 'debit' ? 'badge-warning' : 'badge-success' }}">
                                {{ ucfirst($account->normal_balance) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $account->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                {{ ucfirst($account->status) }}
                            </span>
                        </td>
                        <td class="text-right font-weight-bold">{{ number_format($account->current_balance, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endisset
        @empty
            <div class="p-4 text-center text-muted">
                <p>No accounts found. Initialize the default chart of accounts to get started.</p>
                <a href="{{ route('accounts.initialize') }}" class="btn btn-success">
                    <i class="fas fa-magic"></i> Initialize Default Accounts
                </a>
            </div>
        @endforelse
    </div>
</div>
@stop
