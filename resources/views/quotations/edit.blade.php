@extends('adminlte::page')

@section('title', 'Edit Quotation')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Quotation: {{ $quotation->quotation_number }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quotations.show', $quotation) }}">{{ $quotation->quotation_number }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(in_array($quotation->status, ['accepted', 'converted']))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                This quotation has been {{ $quotation->status }} and cannot be modified.
            </div>
        @endif

        <form method="POST" action="{{ route('quotations.update', $quotation) }}" id="quotationForm">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quotation Information</h3>
                            <div class="card-tools">
                                <button type="submit" class="btn btn-primary" {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i> Update Quotation
                                </button>
                                <button type="button" class="btn btn-info" id="previewQuotation">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to View
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Client <span class="text-danger">*</span></label>
                                        <select class="form-control @error('client_id') is-invalid @enderror" 
                                                id="client_id" name="client_id" required
                                                {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                                            <option value="">Select a client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" 
                                                        {{ old('client_id', $quotation->client_id) == $client->id ? 'selected' : '' }}>
                                                    {{ $client->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('client_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quotation_number">Quotation Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('quotation_number') is-invalid @enderror" 
                                               id="quotation_number" name="quotation_number" 
                                               value="{{ old('quotation_number', $quotation->quotation_number) }}" required
                                               {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                        @error('quotation_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quotation_date">Quotation Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('quotation_date') is-invalid @enderror" 
                                               id="quotation_date" name="quotation_date" 
                                               value="{{ old('quotation_date', $quotation->quotation_date->format('Y-m-d')) }}" required
                                               {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                        @error('quotation_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="valid_until">Valid Until <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                               id="valid_until" name="valid_until" 
                                               value="{{ old('valid_until', $quotation->valid_until->format('Y-m-d')) }}" required
                                               {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                        @error('valid_until')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference_number">Reference Number</label>
                                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                               id="reference_number" name="reference_number" 
                                               value="{{ old('reference_number', $quotation->reference_number) }}" 
                                               placeholder="Optional reference"
                                               {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                        @error('reference_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required
                                                {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                                            <option value="draft" {{ old('status', $quotation->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="sent" {{ old('status', $quotation->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="viewed" {{ old('status', $quotation->status) == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                            <option value="accepted" {{ old('status', $quotation->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                            <option value="rejected" {{ old('status', $quotation->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="expired" {{ old('status', $quotation->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency">Currency <span class="text-danger">*</span></label>
                                        <select class="form-control @error('currency') is-invalid @enderror" 
                                                id="currency" name="currency" required
                                                {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                                            <option value="UGX" {{ old('currency', $quotation->currency) == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                                            <option value="USD" {{ old('currency', $quotation->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                            <option value="EUR" {{ old('currency', $quotation->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                            <option value="GBP" {{ old('currency', $quotation->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        </select>
                                        @error('currency')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quotation Items</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" id="addItem"
                                        {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                                <button type="button" class="btn btn-info btn-sm" id="addFromCatalog"
                                        {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                                    <i class="fas fa-list"></i> Add from Catalog
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th>Service/Item</th>
                                            <th>Description</th>
                                            <th width="10%">Qty</th>
                                            <th width="10%">Unit</th>
                                            <th width="15%">Unit Price</th>
                                            <th width="15%">Total</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        @foreach($quotation->quotationItems as $index => $quotationItem)
                                            <tr class="item-row" data-index="{{ $index }}">
                                                <td>
                                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $quotationItem->item_id }}">
                                                    <input type="text" class="form-control item-name" 
                                                           name="items[{{ $index }}][item_name]" 
                                                           value="{{ old('items.'.$index.'.item_name', $quotationItem->item_name) }}" 
                                                           placeholder="Service/Item name" required
                                                           {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                                </td>
                                                <td>
                                                    <textarea class="form-control item-description" 
                                                              name="items[{{ $index }}][item_description]" 
                                                              placeholder="Service/Item description"
                                                              {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>{{ old('items.'.$index.'.item_description', $quotationItem->item_description) }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control quantity" 
                                                           name="items[{{ $index }}][quantity]" 
                                                           value="{{ old('items.'.$index.'.quantity', $quotationItem->quantity) }}" 
                                                           step="0.01" min="0.01" required
                                                           {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control unit-type" 
                                                           name="items[{{ $index }}][unit_type]" 
                                                           value="{{ old('items.'.$index.'.unit_type', $quotationItem->unit_type) }}" 
                                                           placeholder="hrs, pcs, etc." required
                                                           {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control unit-price" 
                                                           name="items[{{ $index }}][unit_price]" 
                                                           value="{{ old('items.'.$index.'.unit_price', $quotationItem->unit_price) }}" 
                                                           step="0.01" min="0" required
                                                           {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                                </td>
                                                <td>
                                                    <span class="line-total">{{ $settings->formatCurrency($quotationItem->line_total) }}</span>
                                                </td>
                                                <td>
                                                    @if(!in_array($quotation->status, ['accepted', 'converted']))
                                                        <button type="button" class="btn btn-danger btn-sm remove-item">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totals Section -->
            <div class="row">
                <div class="col-md-8">
                    <!-- Discount Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Discount</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discount_type">Discount Type</label>
                                        <select class="form-control" id="discount_type" name="discount_type"
                                                {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                                            <option value="">No Discount</option>
                                            <option value="fixed" {{ old('discount_type', $quotation->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                            <option value="percentage" {{ old('discount_type', $quotation->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discount_value">Discount Value</label>
                                        <input type="number" class="form-control" id="discount_value" name="discount_value" 
                                               value="{{ old('discount_value', $quotation->discount_value) }}" 
                                               step="0.01" min="0"
                                               {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Summary</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Subtotal:</th>
                                    <td class="text-right" id="subtotalDisplay">{{ $settings->formatCurrency($quotation->subtotal) }}</td>
                                </tr>
                                <tr>
                                    <th>Tax:</th>
                                    <td class="text-right" id="taxDisplay">{{ $settings->formatCurrency($quotation->tax_amount) }}</td>
                                </tr>
                                <tr>
                                    <th>Discount:</th>
                                    <td class="text-right" id="discountDisplay">{{ $settings->formatCurrency($quotation->discount_amount) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Total:</th>
                                    <th class="text-right" id="totalDisplay">{{ $settings->formatCurrency($quotation->total_amount) }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes and Terms Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Terms & Conditions</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <textarea class="form-control @error('terms') is-invalid @enderror" 
                                          id="terms" name="terms" rows="4" 
                                          placeholder="Payment terms and conditions"
                                          {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>{{ old('terms', $quotation->terms) }}</textarea>
                                @error('terms')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notes</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="4" 
                                          placeholder="Internal notes for this quotation"
                                          {{ in_array($quotation->status, ['accepted', 'converted']) ? 'readonly' : '' }}>{{ old('notes', $quotation->notes) }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary btn-lg" {{ in_array($quotation->status, ['accepted', 'converted']) ? 'disabled' : '' }}>
                        <i class="fas fa-save"></i> Update Quotation
                    </button>
                    <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Item Catalog Modal -->
    <div class="modal fade" id="itemCatalogModal" tabindex="-1" role="dialog" aria-labelledby="itemCatalogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemCatalogModalLabel">Select from Catalog</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Unit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $settings->formatCurrency($item->unit_price) }}</td>
                                        <td>{{ $item->unit_type }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm select-item" 
                                                    data-item-id="{{ $item->id }}"
                                                    data-item-name="{{ $item->name }}"
                                                    data-item-description="{{ $item->description }}"
                                                    data-item-price="{{ $item->unit_price }}"
                                                    data-item-unit="{{ $item->unit_type }}">
                                                Select
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .item-row input, .item-row textarea {
            border: none;
            background: transparent;
        }
        .item-row input:focus, .item-row textarea:focus {
            border: 1px solid #007bff;
            background: white;
        }
        .table th {
            border-top: none;
        }
    </style>
@stop

@section('js')
<script>
$(document).ready(function() {
    let itemIndex = {{ count($quotation->quotationItems) }};
    const isReadonly = {{ in_array($quotation->status, ['accepted', 'converted']) ? 'true' : 'false' }};
    
    // Add new item row
    $('#addItem').click(function() {
        if (isReadonly) return;
        
        addItemRow();
    });
    
    // Add from catalog
    $('#addFromCatalog').click(function() {
        if (isReadonly) return;
        
        $('#itemCatalogModal').modal('show');
    });
    
    // Select item from catalog
    $('.select-item').click(function() {
        const itemData = {
            id: $(this).data('item-id'),
            name: $(this).data('item-name'),
            description: $(this).data('item-description'),
            price: $(this).data('item-price'),
            unit: $(this).data('item-unit')
        };
        
        addItemRow(itemData);
        $('#itemCatalogModal').modal('hide');
    });
    
    // Remove item row
    $(document).on('click', '.remove-item', function() {
        if (isReadonly) return;
        
        $(this).closest('tr').remove();
        recalculateTotals();
        reindexItems();
    });
    
    // Calculate totals on input change
    $(document).on('input', '.quantity, .unit-price', function() {
        calculateLineTotal($(this).closest('tr'));
        recalculateTotals();
    });
    
    // Calculate discount
    $('#discount_type, #discount_value').on('input change', function() {
        recalculateTotals();
    });
    
    // Preview quotation
    $('#previewQuotation').click(function() {
        window.open('{{ route("quotations.preview", $quotation) }}', '_blank');
    });
    
    function addItemRow(itemData = null) {
        const row = `
            <tr class="item-row" data-index="${itemIndex}">
                <td>
                    <input type="hidden" name="items[${itemIndex}][item_id]" value="${itemData ? itemData.id : ''}">
                    <input type="text" class="form-control item-name" 
                           name="items[${itemIndex}][item_name]" 
                           value="${itemData ? itemData.name : ''}" 
                           placeholder="Service/Item name" required>
                </td>
                <td>
                    <textarea class="form-control item-description" 
                              name="items[${itemIndex}][item_description]" 
                              placeholder="Service/Item description">${itemData ? itemData.description : ''}</textarea>
                </td>
                <td>
                    <input type="number" class="form-control quantity" 
                           name="items[${itemIndex}][quantity]" 
                           value="1" step="0.01" min="0.01" required>
                </td>
                <td>
                    <input type="text" class="form-control unit-type" 
                           name="items[${itemIndex}][unit_type]" 
                           value="${itemData ? itemData.unit : 'hrs'}" 
                           placeholder="hrs, pcs, etc." required>
                </td>
                <td>
                    <input type="number" class="form-control unit-price" 
                           name="items[${itemIndex}][unit_price]" 
                           value="${itemData ? itemData.price : '0'}" 
                           step="0.01" min="0" required>
                </td>
                <td>
                    <span class="line-total">0.00</span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#itemsTableBody').append(row);
        itemIndex++;
        
        if (itemData) {
            calculateLineTotal($('#itemsTableBody tr:last'));
            recalculateTotals();
        }
    }
    
    function calculateLineTotal(row) {
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        const lineTotal = quantity * unitPrice;
        
        row.find('.line-total').text(formatCurrency(lineTotal));
    }
    
    function recalculateTotals() {
        let subtotal = 0;
        
        $('.item-row').each(function() {
            const quantity = parseFloat($(this).find('.quantity').val()) || 0;
            const unitPrice = parseFloat($(this).find('.unit-price').val()) || 0;
            subtotal += quantity * unitPrice;
        });
        
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val()) || 0;
        let discount = 0;
        
        if (discountType === 'percentage') {
            discount = subtotal * (discountValue / 100);
        } else if (discountType === 'fixed') {
            discount = discountValue;
        }
        
        const total = subtotal - discount;
        
        $('#subtotalDisplay').text(formatCurrency(subtotal));
        $('#taxDisplay').text(formatCurrency(0)); // No tax for now
        $('#discountDisplay').text(formatCurrency(discount));
        $('#totalDisplay').text(formatCurrency(total));
    }
    
    function reindexItems() {
        $('#itemsTableBody tr.item-row').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('input, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, `[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
        itemIndex = $('#itemsTableBody tr.item-row').length;
    }
    
    function formatCurrency(amount) {
        const currency = $('#currency').val() || 'UGX';
        return currency + ' ' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // Initial calculation
    recalculateTotals();
});
</script>
@stop