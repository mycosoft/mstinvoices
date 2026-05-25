@extends('adminlte::page')

@section('title', 'Inventory Overview')

@section('content_header')
    <h1>Inventory Overview</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Products</span>
                <span class="info-box-number">{{ $items->total() }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-cubes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Items in Stock</span>
                <span class="info-box-number">{{ number_format($totalItems) }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Stock Value</span>
                <span class="info-box-number">{{ number_format($totalValue, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Low Stock Alerts</span>
                <span class="info-box-number">{{ $lowStockCount }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>Tracked Items</strong>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th class="text-right">Cost Price</th>
                    <th class="text-right">Sell Price</th>
                    <th class="text-right">In Stock</th>
                    <th class="text-right">Alert Level</th>
                    <th class="text-right">Stock Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="{{ $item->stock_quantity <= ($item->low_stock_alert ?? 0) ? 'table-danger' : '' }}">
                    <td>
                        <a href="{{ route('items.show', $item) }}">{{ $item->name }}</a>
                        @if($item->is_service)
                            <span class="badge badge-info">Service</span>
                        @endif
                    </td>
                    <td>{{ $item->category ?: '-' }}</td>
                    <td class="text-right">{{ number_format($item->cost_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right font-weight-bold">{{ number_format($item->stock_quantity ?? 0) }}</td>
                    <td class="text-right">{{ $item->low_stock_alert ?? '-' }}</td>
                    <td class="text-right">{{ number_format(($item->stock_quantity ?? 0) * ($item->cost_price ?? 0), 2) }}</td>
                    <td>
                        @if($item->stock_quantity <= 0)
                            <span class="badge badge-danger">Out of Stock</span>
                        @elseif($item->stock_quantity <= ($item->low_stock_alert ?? 0))
                            <span class="badge badge-warning">Low Stock</span>
                        @else
                            <span class="badge badge-success">In Stock</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted p-3">No inventory items tracked. Edit items to enable inventory tracking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $items->links() }}</div>
</div>
@stop
