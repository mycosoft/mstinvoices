@extends('adminlte::page')

@section('title', $posSale->pos_number)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><a href="{{ route('pos.index') }}">POS Sales</a> / {{ $posSale->pos_number }}</h1>
        <div>
            <a href="{{ route('pos.receipt', $posSale) }}" class="btn btn-secondary btn-sm"><i class="fas fa-receipt"></i> Receipt PDF</a>
            @if(!$posSale->invoice_id && $posSale->client_id)
                <form method="POST" action="{{ route('pos.link-to-invoice', $posSale) }}" style="display:inline"
                      onsubmit="return confirm('Create an invoice from this POS sale?')">
                    @csrf
                    <button class="btn btn-warning btn-sm"><i class="fas fa-file-invoice"></i> Link to Invoice</button>
                </form>
            @endif
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><strong>Sale Details</strong></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th style="width:140px">POS #:</th><td>{{ $posSale->pos_number }}</td></tr>
                    <tr><th>Date:</th><td>{{ $posSale->sale_date->format('Y-m-d') }}</td></tr>
                    <tr><th>Customer:</th><td>{{ $posSale->client?->name ?? 'Walk-in Customer' }}</td></tr>
                    <tr><th>Cashier:</th><td>{{ $posSale->cashier_name }}</td></tr>
                    <tr><th>Payment Method:</th><td>{{ ucfirst(str_replace('_', ' ', $posSale->payment_method)) }}</td></tr>
                    <tr><th>Status:</th><td>
                        <span class="badge badge-{{ $posSale->status === 'completed' ? 'success' : ($posSale->status === 'refunded' ? 'warning' : 'danger') }}">
                            {{ ucfirst($posSale->status) }}
                        </span>
                    </td></tr>
                    @if($posSale->invoice)
                    <tr><th>Linked Invoice:</th><td><a href="{{ route('invoices.show', $posSale->invoice) }}">{{ $posSale->invoice->invoice_number }}</a></td></tr>
                    @endif
                </table>
            </div>
        </div>
        @if($posSale->notes)
        <div class="card">
            <div class="card-header"><strong>Notes</strong></div>
            <div class="card-body">{{ $posSale->notes }}</div>
        </div>
        @endif
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><strong>Sale Items</strong></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posSale->saleItems as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 0) }}</td>
                            <td class="text-right">{{ number_format($item->total_amount, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3" class="text-right">Subtotal:</th><td class="text-right">{{ number_format($posSale->subtotal, 0) }}</td></tr>
                        @if($posSale->tax_amount > 0)
                        <tr><th colspan="3" class="text-right">Tax:</th><td class="text-right">{{ number_format($posSale->tax_amount, 0) }}</td></tr>
                        @endif
                        @if($posSale->discount_amount > 0)
                        <tr><th colspan="3" class="text-right">Discount:</th><td class="text-right">({{ number_format($posSale->discount_amount, 0) }})</td></tr>
                        @endif
                        <tr class="font-weight-bold"><th colspan="3" class="text-right">Total:</th><td class="text-right">{{ number_format($posSale->total_amount, 0) }}</td></tr>
                        <tr><th colspan="3" class="text-right">Paid:</th><td class="text-right text-success">{{ number_format($posSale->paid_amount, 0) }}</td></tr>
                        <tr><th colspan="3" class="text-right text-info">Change:</th><td class="text-right text-info">{{ number_format($posSale->change_amount, 0) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
