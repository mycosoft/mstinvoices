@extends('adminlte::page')

@section('title', 'Stock Movements')

@section('content_header')
    <h1><a href="{{ route('inventory.index') }}">Inventory</a> / Stock Movements</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex gap-2">
            <select name="type" class="form-control form-control-sm" style="max-width:150px">
                <option value="">All Types</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
            </select>
            <select name="item_id" class="form-control form-control-sm" style="max-width:200px">
                <option value="">All Items</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Total Value</th>
                    <th>Reference</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date->format('Y-m-d') }}</td>
                    <td>{{ $movement->item->name }}</td>
                    <td>
                        <span class="badge {{ $movement->type === 'in' || $movement->type === 'return' ? 'badge-success' : ($movement->type === 'out' ? 'badge-danger' : 'badge-info') }}">
                            {{ $movement->type_label }}
                        </span>
                    </td>
                    <td class="text-right font-weight-bold">{{ number_format($movement->quantity) }}</td>
                    <td class="text-right">{{ number_format($movement->unit_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($movement->quantity * $movement->unit_cost, 2) }}</td>
                    <td>{{ $movement->reference_type ? str_replace('_', ' ', ucfirst($movement->reference_type)) : '-' }}</td>
                    <td>{{ Str::limit($movement->notes, 40) }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted p-3">No stock movements recorded</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $movements->links() }}</div>
</div>
@stop
