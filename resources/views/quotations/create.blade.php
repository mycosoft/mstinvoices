@extends('adminlte::page')

@section('title', 'Create Quotation')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create New Quotation</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
                    <li class="breadcrumb-item active">Create</li>
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

        <form method="POST" action="{{ route('quotations.store') }}">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quotation Information</h3>
                            <div class="card-tools">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Quotation
                                </button>
                                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Client <span class="text-danger">*</span></label>
                                        <select class="form-control" id="client_id" name="client_id" required>
                                            <option value="">Select a client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quotation_number">Quotation Number</label>
                                        <input type="text" class="form-control" id="quotation_number" name="quotation_number" 
                                               value="{{ old('quotation_number', $quotationNumber) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quotation_date">Quotation Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="quotation_date" name="quotation_date" 
                                               value="{{ old('quotation_date', date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="valid_until">Valid Until <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="valid_until" name="valid_until" 
                                               value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference_number">Reference Number</label>
                                        <input type="text" class="form-control" id="reference_number" name="reference_number" 
                                               value="{{ old('reference_number') }}" placeholder="Optional reference">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency">Currency <span class="text-danger">*</span></label>
                                        <select class="form-control" id="currency" name="currency" required>
                                            <option value="UGX" {{ old('currency', $settings->default_currency ?? 'UGX') == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                                            <option value="USD" {{ old('currency', $settings->default_currency ?? 'UGX') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                            <option value="EUR" {{ old('currency', $settings->default_currency ?? 'UGX') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                            <option value="GBP" {{ old('currency', $settings->default_currency ?? 'UGX') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        </select>
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
                                <button type="button" class="btn btn-success btn-sm" id="addItem">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                                <button type="button" class="btn btn-info btn-sm" id="addFromCatalog">
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
                                        <!-- Items will be added dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totals Section -->
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Summary</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Subtotal:</th>
                                    <td class="text-right" id="subtotalDisplay">{{ $settings->formatCurrency(0) }}</td>
                                </tr>
                                <tr>
                                    <th>Tax:</th>
                                    <td class="text-right" id="taxDisplay">{{ $settings->formatCurrency(0) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Total:</th>
                                    <th class="text-right" id="totalDisplay">{{ $settings->formatCurrency(0) }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Additional Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Internal notes for this quotation">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Quotation
                    </button>
                    <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-lg">
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
                    <h5 class="modal-title" id="itemCatalogModalLabel">Select from Service/Item Catalog</h5>
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
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td><strong>{{ $item->name }}</strong></td>
                                        <td>{{ Str::limit($item->description, 50) }}</td>
                                        <td>{{ $item->formatted_unit_price }}</td>
                                        <td>{{ $item->unit_type }}</td>
                                        <td>
                                            @if($item->is_service)
                                                <span class="badge badge-info">Service</span>
                                            @else
                                                <span class="badge badge-success">Item</span>
                                            @endif
                                        </td>
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

    <!-- Hidden input template for items -->
    <input type="hidden" id="currency" value="{{ $settings->currency ?? 'UGX' }}">
    <input type="hidden" id="currencySymbol" value="{{ $settings->currency_symbol ?? 'UGX' }}">
@stop

@section('css')
    <style>
        .table th {
            border-top: none;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .item-row {
            background-color: #f9f9f9;
        }
        .remove-item {
            color: #dc3545;
            cursor: pointer;
        }
    </style>
@stop

@section('js')
    <script>
        // Ensure jQuery is loaded before running the script
        function initQuotationCreate() {
            if (typeof $ === 'undefined') {
                console.log('jQuery not loaded yet, retrying in 100ms...');
                setTimeout(initQuotationCreate, 100);
                return;
            }
            
            console.log('jQuery loaded successfully for quotation create');
            
        $(document).ready(function() {
            let itemIndex = 0;
            
            // Add item functionality
            $('#addItem').click(function() {
                addItemRow();
            });
            
            // Add from catalog functionality
            $('#addFromCatalog').click(function() {
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
            
            // Add initial item row
            addItemRow();
            
            function addItemRow(itemData = null) {
                const html = `
                    <tr class="item-row" data-index="${itemIndex}">
                        <td>
                            <input type="hidden" name="items[${itemIndex}][item_id]" value="${itemData ? itemData.id : ''}">
                            <input type="text" class="form-control" name="items[${itemIndex}][item_name]" 
                                   placeholder="Service/Item name" value="${itemData ? itemData.name : ''}" required>
                        </td>
                        <td>
                            <textarea class="form-control" name="items[${itemIndex}][item_description]" 
                                      placeholder="Description" rows="2">${itemData ? itemData.description : ''}</textarea>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity" name="items[${itemIndex}][quantity]" 
                                   step="0.01" min="0.01" value="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="items[${itemIndex}][unit_type]" 
                                   placeholder="hrs, pcs, etc." value="${itemData ? itemData.unit : 'hrs'}" required>
                        </td>
                        <td>
                            <input type="number" class="form-control unit-price" name="items[${itemIndex}][unit_price]" 
                                   step="0.01" min="0" placeholder="0.00" value="${itemData ? itemData.price : ''}" required>
                        </td>
                        <td>
                            <span class="line-total">{{ $settings->formatCurrency(0) }}</span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-item" title="Remove item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#itemsTableBody').append(html);
                itemIndex++;
                
                if (itemData) {
                    updateLineTotal($('#itemsTableBody tr:last'));
                }
                updateTotals();
            }
            
            // Remove item functionality
            $(document).on('click', '.remove-item', function() {
                if ($('#itemsTableBody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateTotals();
                } else {
                    alert('At least one item is required.');
                }
            });
            
            // Update totals when quantity or price changes
            $(document).on('input', '.quantity, .unit-price', function() {
                updateLineTotal($(this).closest('tr'));
                updateTotals();
            });
            
            function updateLineTotal(row) {
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
                const lineTotal = quantity * unitPrice;
                
                row.find('.line-total').text(formatCurrency(lineTotal));
            }
            
            function updateTotals() {
                let subtotal = 0;
                
                $('.item-row').each(function() {
                    const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                    const unitPrice = parseFloat($(this).find('.unit-price').val()) || 0;
                    subtotal += quantity * unitPrice;
                });
                
                const tax = 0; // For now, no tax calculation
                const total = subtotal + tax;
                
                $('#subtotalDisplay').text(formatCurrency(subtotal));
                $('#taxDisplay').text(formatCurrency(tax));
                $('#totalDisplay').text(formatCurrency(total));
            }
            
            function formatCurrency(amount) {
                const currency = $('#currency').val() || 'UGX';
                return currency + ' ' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
        });
        }
        
        // Start the initialization
        initQuotationCreate();
    </script>
@stop