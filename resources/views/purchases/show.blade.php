@extends('adminlte::page')

@section('title', $purchase->purchase_number)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><a href="{{ route('purchases.index') }}">Purchases</a> / {{ $purchase->purchase_number }}</h1>
        <div>
            @if(in_array($purchase->status, ['ordered', 'draft']))
                <form method="POST" action="{{ route('purchases.receive', $purchase) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Receive Goods</button>
                </form>
                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            @endif
            @if(in_array($purchase->status, ['draft', 'ordered']))
                <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" style="display:inline"
                      onsubmit="return confirm('Delete this purchase?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                </form>
            @endif
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Purchase Details</strong></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th style="width:140px">Purchase #:</th><td>{{ $purchase->purchase_number }}</td></tr>
                    <tr><th>Reference:</th><td>{{ $purchase->reference ?? '-' }}</td></tr>
                    <tr><th>Supplier:</th><td>{{ $purchase->supplier?->name ?? 'N/A' }}</td></tr>
                    <tr><th>Purchase Date:</th><td>{{ $purchase->purchase_date->format('Y-m-d') }}</td></tr>
                    <tr><th>Delivery Date:</th><td>{{ $purchase->delivery_date?->format('Y-m-d') ?? 'Not set' }}</td></tr>
                    <tr><th>Warehouse:</th><td>{{ $purchase->warehouse?->name ?? 'Default' }}</td></tr>
                    <tr><th>Status:</th><td>
                        <span class="badge badge-{{ $purchase->status === 'paid' ? 'success' : ($purchase->status === 'received' ? 'info' : ($purchase->status === 'ordered' ? 'warning' : ($purchase->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </td></tr>
                    <tr><th>Payment:</th><td>
                        @if($purchase->payment_status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge badge-warning">Partial</span>
                        @else
                            <span class="badge badge-danger">Unpaid</span>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>
        @if($purchase->notes)
        <div class="card">
            <div class="card-header"><strong>Notes</strong></div>
            <div class="card-body">{{ $purchase->notes }}</div>
        </div>
        @endif
        @if($purchase->terms)
        <div class="card">
            <div class="card-header"><strong>Terms & Conditions</strong></div>
            <div class="card-body">{{ $purchase->terms }}</div>
        </div>
        @endif
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Financial Summary</strong></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Subtotal:</th><td class="text-right">{{ number_format($purchase->subtotal, 0) }}</td></tr>
                    <tr><th>Tax Amount:</th><td class="text-right">{{ number_format($purchase->tax_amount, 0) }}</td></tr>
                    @if($purchase->discount_amount > 0)
                    <tr><th>Discount:</th><td class="text-right">({{ number_format($purchase->discount_amount, 0) }})</td></tr>
                    @endif
                    <tr class="font-weight-bold"><th>Total Amount:</th><td class="text-right">{{ number_format($purchase->total_amount, 0) }}</td></tr>
                    <tr><th>Paid Amount:</th><td class="text-right text-success">{{ number_format($purchase->paid_amount, 0) }}</td></tr>
                    <tr class="font-weight-bold {{ $purchase->balance_due > 0 ? 'text-danger' : 'text-success' }}"><th>Balance Due:</th><td class="text-right">{{ number_format($purchase->balance_due, 0) }}</td></tr>
                </table>
            </div>
        </div>

        @if($purchase->balance_due > 0)
        <div class="card card-success">
            <div class="card-header"><strong>Add Payment</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('purchases.add-payment', $purchase) }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" name="amount" class="form-control" max="{{ $purchase->balance_due }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reference</label>
                                <input type="text" name="reference" class="form-control" placeholder="Transaction ref">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Notes</label>
                                <input type="text" name="notes" class="form-control" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-money-bill"></i> Record Payment</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Purchase Items</strong></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Line Total</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format($item->unit_cost, 0) }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 0) }}</td>
                    <td class="text-right">{{ $item->tax_amount > 0 ? number_format($item->tax_amount, 0) : '-' }}</td>
                    <td class="text-right">{{ number_format($item->total_amount, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($purchase->payments->count() > 0)
<div class="card">
    <div class="card-header"><strong>Payment History</strong></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                    <td>{{ number_format($payment->amount, 0) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td>{{ $payment->reference ?? '-' }}</td>
                    <td>{{ $payment->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@stop
