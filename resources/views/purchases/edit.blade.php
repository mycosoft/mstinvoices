@extends('adminlte::page')

@section('title', 'Edit Purchase')

@section('content_header')
    <h1><a href="{{ route('purchases.index') }}">Purchases</a> / Edit {{ $purchase->purchase_number }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('purchases.update', $purchase) }}" id="purchase-form">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $purchase->delivery_date ? $purchase->delivery_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Reference</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference', $purchase->reference) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Warehouse</label>
                        <select name="warehouse_id" class="form-control">
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $purchase->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h5>Purchase Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:30%">Item</th>
                            <th style="width:10%">Quantity</th>
                            <th style="width:15%">Unit Cost</th>
                            <th style="width:10%">Tax %</th>
                            <th style="width:15%">Line Total</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        @foreach($purchase->items as $i => $item)
                        <tr class="item-row">
                            <td>
                                <select name="items[{{ $i }}][item_id]" class="form-control form-control-sm item-select">
                                    <option value="">Select item or type name below</option>
                                    @foreach($items as $itm)
                                        <option value="{{ $itm->id }}" data-price="{{ $itm->cost_price }}" {{ $item->item_id == $itm->id ? 'selected' : '' }}>{{ $itm->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="items[{{ $i }}][item_name]" class="form-control form-control-sm mt-1" value="{{ $item->item_name }}" placeholder="Or enter new item name">
                            </td>
                            <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm qty" value="{{ $item->quantity }}" min="0.01" step="0.01"></td>
                            <td><input type="number" name="items[{{ $i }}][unit_cost]" class="form-control form-control-sm unit-cost" value="{{ $item->unit_cost }}" min="0" step="0.01"></td>
                            <td><input type="number" name="items[{{ $i }}][tax_rate]" class="form-control form-control-sm tax-rate" value="{{ $item->tax_rate }}" min="0" max="100" step="0.01"></td>
                            <td><span class="line-total">{{ number_format($item->total_amount, 2) }}</span></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-times"></i></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-success" id="add-item"><i class="fas fa-plus"></i> Add Item</button>

            <hr>
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-sm">
                        <tr><th>Subtotal:</th><td class="text-right"><span id="subtotal">{{ number_format($purchase->subtotal, 2) }}</span></td></tr>
                        <tr><th>Tax Total:</th><td class="text-right"><span id="tax-total">{{ number_format($purchase->tax_amount, 2) }}</span></td></tr>
                        <tr><th>Grand Total:</th><td class="text-right"><strong><span id="grand-total">{{ number_format($purchase->total_amount, 2) }}</span></strong></td></tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $purchase->notes) }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Terms & Conditions</label>
                        <textarea name="terms" class="form-control" rows="3">{{ old('terms', $purchase->terms) }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Purchase</button>
            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop

@push('js')
<script>
let itemIndex = {{ count($purchase->items) }};
document.getElementById('add-item')?.addEventListener('click', function() {
    const row = document.querySelector('.item-row').cloneNode(true);
    row.querySelectorAll('input, select').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, '[' + itemIndex + ']');
        if (el.type === 'number') el.value = el.classList.contains('qty') ? '1' : '0';
        else if (el.tagName === 'INPUT') el.value = '';
        else el.value = '';
    });
    row.querySelector('.line-total').textContent = '0.00';
    document.getElementById('items-container').appendChild(row);
    itemIndex++;
    attachRowEvents(row);
});
function attachRowEvents(row) {
    row.querySelectorAll('.qty, .unit-cost, .tax-rate').forEach(el => { el.addEventListener('input', calculateRow); });
    row.querySelector('.item-select')?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.dataset.price) row.querySelector('.unit-cost').value = opt.dataset.price;
        calculateRow.call({target: this});
    });
    row.querySelector('.remove-item')?.addEventListener('click', function() {
        if (document.querySelectorAll('.item-row').length > 1) { row.remove(); calculateTotals(); }
    });
}
document.querySelectorAll('.item-row').forEach(attachRowEvents);
function calculateRow() {
    const row = this.closest ? this.closest('tr') : this;
    const qty = parseFloat(row.querySelector('.qty').value) || 0;
    const cost = parseFloat(row.querySelector('.unit-cost').value) || 0;
    const tax = parseFloat(row.querySelector('.tax-rate').value) || 0;
    const lineTotal = qty * cost;
    row.querySelector('.line-total').textContent = (lineTotal + lineTotal * (tax / 100)).toFixed(2);
    calculateTotals();
}
function calculateTotals() {
    let subtotal = 0, taxTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const cost = parseFloat(row.querySelector('.unit-cost').value) || 0;
        const tax = parseFloat(row.querySelector('.tax-rate').value) || 0;
        const lineTotal = qty * cost;
        subtotal += lineTotal;
        taxTotal += lineTotal * (tax / 100);
    });
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('tax-total').textContent = taxTotal.toFixed(2);
    document.getElementById('grand-total').textContent = (subtotal + taxTotal).toFixed(2);
}
</script>
@endpush
