@extends('adminlte::page')

@section('title', 'POS Sales')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>POS Sales</h1>
        <a href="{{ route('pos.terminal') }}" class="btn btn-success btn-sm">
            <i class="fas fa-shopping-cart"></i> Open POS Terminal
        </a>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('pos.index') }}" class="d-flex gap-2 flex-wrap">
            <select name="status" class="form-control form-control-sm" style="max-width:140px">
                <option value="">All Status</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="voided" {{ request('status') == 'voided' ? 'selected' : '' }}>Voided</option>
            </select>
            <input type="date" name="date_from" class="form-control form-control-sm" style="max-width:160px" value="{{ request('date_from') }}">
            <input type="date" name="date_to" class="form-control form-control-sm" style="max-width:160px" value="{{ request('date_to') }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            <a href="{{ route('pos.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>POS #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Cashier</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td><a href="{{ route('pos.show', $sale) }}">{{ $sale->pos_number }}</a></td>
                    <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                    <td>{{ $sale->client?->name ?? 'Walk-in' }}</td>
                    <td>{{ $sale->cashier_name }}</td>
                    <td class="text-right">{{ number_format($sale->total_amount, 0) }}</td>
                    <td class="text-right">{{ number_format($sale->paid_amount, 0) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                    <td>
                        @if($sale->status === 'completed')
                            <span class="badge badge-success">Completed</span>
                        @elseif($sale->status === 'refunded')
                            <span class="badge badge-warning">Refunded</span>
                        @else
                            <span class="badge badge-danger">Voided</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('pos.show', $sale) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('pos.receipt', $sale) }}" class="btn btn-sm btn-secondary"><i class="fas fa-receipt"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4">No POS sales yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())
    <div class="card-footer">
        {{ $sales->links() }}
    </div>
    @endif
</div>
@stop
