@extends('adminlte::page')

@section('title', $supplier->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><a href="{{ route('suppliers.index') }}">Suppliers</a> / {{ $supplier->name }}</h1>
        <div>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display:inline"
                  onsubmit="return confirm('Delete this supplier?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
            </form>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><strong>Supplier Details</strong></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th style="width:140px">Name:</th><td>{{ $supplier->name }}</td></tr>
                    <tr><th>Company:</th><td>{{ $supplier->company_name ?? '-' }}</td></tr>
                    <tr><th>Email:</th><td>{{ $supplier->email ?? '-' }}</td></tr>
                    <tr><th>Phone:</th><td>{{ $supplier->phone ?? '-' }}</td></tr>
                    <tr><th>Tax Number:</th><td>{{ $supplier->tax_number ?? '-' }}</td></tr>
                    <tr><th>Payment Terms:</th><td>{{ $supplier->payment_terms ? $supplier->payment_terms . ' days' : '-' }}</td></tr>
                    <tr><th>Status:</th><td>
                        @if($supplier->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>
        @if($supplier->address || $supplier->city || $supplier->country)
        <div class="card">
            <div class="card-header"><strong>Address</strong></div>
            <div class="card-body">
                <p>{{ $supplier->address }}<br>
                   {{ $supplier->city }}{{ $supplier->state ? ', ' . $supplier->state : '' }}<br>
                   {{ $supplier->postal_code ?? '' }}<br>
                   {{ $supplier->country ?? '' }}</p>
            </div>
        </div>
        @endif
        @if($supplier->notes)
        <div class="card">
            <div class="card-header"><strong>Notes</strong></div>
            <div class="card-body">
                <p>{{ $supplier->notes }}</p>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Purchase History</strong>
                <a href="{{ route('purchases.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> New Purchase
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->purchases as $purchase)
                        <tr>
                            <td><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->purchase_number }}</a></td>
                            <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($purchase->total_amount, 0) }}</td>
                            <td>{{ number_format($purchase->paid_amount, 0) }}</td>
                            <td>{{ number_format($purchase->balance_due, 0) }}</td>
                            <td>
                                <span class="badge badge-{{ $purchase->status === 'paid' ? 'success' : ($purchase->status === 'received' ? 'info' : ($purchase->status === 'ordered' ? 'warning' : 'secondary')) }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td><a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-3">No purchases yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
