@extends('adminlte::page')

@section('title', 'Stock Adjustments')

@section('content_header')
    <h1><a href="{{ route('inventory.index') }}">Inventory</a> / Stock Adjustments</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><strong>New Adjustment</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('inventory.adjustments.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Item <span class="text-danger">*</span></label>
                        <select name="item_id" class="form-control" required>
                            <option value="">Select Item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (Current: {{ $item->stock_quantity ?? 0 }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>New Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="new_quantity" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-check"></i> Apply Adjustment</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><strong>Adjustment History</strong></div>
            <div class="card-body p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>New Quantity</th>
                            <th>Cost Price</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movement->movement_date->format('Y-m-d') }}</td>
                            <td>{{ $movement->item->name }}</td>
                            <td class="font-weight-bold">{{ number_format($movement->quantity) }}</td>
                            <td>{{ number_format($movement->unit_cost, 2) }}</td>
                            <td>{{ $movement->notes }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted p-3">No adjustments yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $movements->links() }}</div>
        </div>
    </div>
</div>
@stop
