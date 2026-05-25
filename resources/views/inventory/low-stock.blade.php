@extends('adminlte::page')

@section('title', 'Low Stock Alerts')

@section('content_header')
    <h1><a href="{{ route('inventory.index') }}">Inventory</a> / Low Stock Alerts</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header bg-danger text-white"><strong>Items Below Alert Level</strong></div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th class="text-right">Current Stock</th>
                    <th class="text-right">Alert Level</th>
                    <th class="text-right">Shortage</th>
                    <th class="text-right">Reorder Point</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="table-danger">
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category ?: '-' }}</td>
                    <td class="text-right font-weight-bold">{{ number_format($item->stock_quantity ?? 0) }}</td>
                    <td class="text-right">{{ $item->low_stock_alert ?? '-' }}</td>
                    <td class="text-right text-danger font-weight-bold">
                        {{ number_format(($item->low_stock_alert ?? 0) - ($item->stock_quantity ?? 0)) }}
                    </td>
                    <td class="text-right">{{ $item->reorder_point ? number_format($item->reorder_point) : '-' }}</td>
                    <td>
                        <a href="{{ route('items.edit', $item) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-success p-3"><i class="fas fa-check-circle"></i> All items are above alert levels!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
