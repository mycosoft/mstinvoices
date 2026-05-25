@extends('adminlte::page')

@section('title', 'Purchases')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Purchases</h1>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Purchase
        </a>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('purchases.index') }}" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:200px"
                   placeholder="Search purchase #..." value="{{ request('search') }}">
            <select name="status" class="form-control form-control-sm" style="max-width:140px">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="payment_status" class="form-control form-control-sm" style="max-width:140px">
                <option value="">Payment</option>
                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            <select name="supplier_id" class="form-control form-control-sm" style="max-width:200px">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Purchase #</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Balance</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->purchase_number }}</a></td>
                    <td>{{ $purchase->supplier?->name ?? 'N/A' }}</td>
                    <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                    <td class="text-right">{{ number_format($purchase->total_amount, 0) }}</td>
                    <td class="text-right">{{ number_format($purchase->paid_amount, 0) }}</td>
                    <td class="text-right">{{ number_format($purchase->balance_due, 0) }}</td>
                    <td>
                        <span class="badge badge-{{ $purchase->status === 'paid' ? 'success' : ($purchase->status === 'received' ? 'info' : ($purchase->status === 'ordered' ? 'warning' : ($purchase->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                        @if($purchase->payment_status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge badge-warning">Partial</span>
                        @elseif($purchase->payment_status === 'unpaid')
                            <span class="badge badge-danger">Unpaid</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4">No purchases found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
    <div class="card-footer">
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@stop
