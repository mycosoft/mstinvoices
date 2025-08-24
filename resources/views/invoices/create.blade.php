@extends('adminlte::page')

@section('title', 'Create Invoice')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create New Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
            @csrf
            <div class="row">
                <!-- Main Invoice Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Invoice Information</h3>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Basic Invoice Details -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Client <span class="text-danger">*</span></label>
                                        <select class="form-control @error('client_id') is-invalid @enderror" 
                                                id="client_id" name="client_id" required>
                                            <option value="">Select a client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
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
                                        <label for="invoice_number">Invoice Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                               id="invoice_number" name="invoice_number" 
                                               value="{{ old('invoice_number', $invoiceNumber) }}" required>
                                        @error('invoice_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="invoice_date">Invoice Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                               id="invoice_date" name="invoice_date" 
                                               value="{{ old('invoice_date', today()->format('Y-m-d')) }}" required>
                                        @error('invoice_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="due_date">Due Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                               id="due_date" name="due_date" 
                                               value="{{ old('due_date', today()->addDays(30)->format('Y-m-d')) }}" required>
                                        @error('due_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="viewed" {{ old('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference_number">Reference Number</label>
                                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                               id="reference_number" name="reference_number" 
                                               value="{{ old('reference_number') }}" placeholder="Client PO number">
                                        @error('reference_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency">Currency <span class="text-danger">*</span></label>
                                        <select class="form-control @error('currency') is-invalid @enderror" 
                                                id="currency" name="currency" required>
                                            <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                        </select>
                                        @error('currency')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Items -->
                            <h5 class="mt-4 mb-3">Invoice Items</h5>
                            <div id="invoice-items">
                                <!-- Items will be dynamically added here -->
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success" id="add-item">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                    <button type="button" class="btn btn-info" id="add-from-catalog">
                                        <i class="fas fa-list"></i> Add from Catalog
                                    </button>
                                </div>
                            </div>

                            <!-- Terms and Notes -->
                            <h5 class="mt-4 mb-3">Additional Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="terms">Payment Terms</label>
                                        <textarea class="form-control @error('terms') is-invalid @enderror" 
                                                  id="terms" name="terms" rows="3" 
                                                  placeholder="Payment terms">{{ old('terms', 'Payment is due within 30 days.') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="3" 
                                                  placeholder="Internal notes">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Invoice
                            </button>
                            <button type="button" class="btn btn-info" id="preview-invoice" disabled>
                                <i class="fas fa-eye"></i> Preview
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Invoice Summary</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Subtotal:</th>
                                    <td class="text-right"><span id="subtotal">{{ $settings->formatCurrency(0) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Tax:</th>
                                    <td class="text-right"><span id="tax-amount">{{ $settings->formatCurrency(0) }}</span></td>
                                </tr>
                                <tr class="border-top">
                                    <th><strong>Total:</strong></th>
                                    <td class="text-right"><strong><span id="total-amount">{{ $settings->formatCurrency(0) }}</span></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Add Item -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Add from Catalog</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <select class="form-control" id="catalog-items">
                                    <option value="">Select an item...</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" 
                                                data-name="{{ $item->name }}"
                                                data-description="{{ $item->description }}"
                                                data-price="{{ $item->unit_price }}"
                                                data-unit="{{ $item->unit_type }}"
                                                data-taxable="{{ $item->is_taxable ? 1 : 0 }}"
                                                data-tax-rate="{{ $item->tax_rate }}">
                                            {{ $item->name }} - {{ $settings->formatCurrency($item->unit_price) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary btn-block" id="add-catalog-item">
                                <i class="fas fa-plus"></i> Add Selected Item
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .invoice-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .item-header {
            background-color: #e9ecef;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-radius: 5px 5px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .remove-item {
            color: #dc3545;
            cursor: pointer;
        }
        .remove-item:hover {
            color: #a71d2a;
        }
    </style>
@stop

@section('js')
    <script>
        let itemCounter = 0;
        
        // Currency settings from server
        const currencySymbol = '{{ $settings->currency_symbol }}';
        const currencyPosition = '{{ $settings->currency_position }}';
        
        function formatCurrency(amount) {
            const formatted = parseFloat(amount).toFixed(2);
            if (currencyPosition === 'before') {
                return currencySymbol + formatted;
            }
            return formatted + currencySymbol;
        }
        
        $(document).ready(function() {
            addInvoiceItem();
            
            $('#add-item').click(function() {
                addInvoiceItem();
            });
            
            $('#add-catalog-item').click(function() {
                const selectedOption = $('#catalog-items option:selected');
                if (selectedOption.val()) {
                    addItemFromCatalog(selectedOption);
                    $('#catalog-items').val('');
                }
            });
            
            $(document).on('input change', '.item-input', function() {
                calculateTotals();
            });
            
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.invoice-item').remove();
                calculateTotals();
                if ($('.invoice-item').length === 0) {
                    addInvoiceItem();
                }
            });
        });
        
        function addInvoiceItem(data = {}) {
            const itemHtml = `
                <div class="invoice-item" data-item="${itemCounter}">
                    <div class="item-header">
                        <strong>Item #${itemCounter + 1}</strong>
                        <i class="fas fa-times remove-item" title="Remove Item"></i>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="items[${itemCounter}][item_name]" 
                                       class="form-control item-input" value="${data.name || ''}" required>
                                <input type="hidden" name="items[${itemCounter}][item_id]" value="${data.id || ''}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" name="items[${itemCounter}][item_description]" 
                                       class="form-control" value="${data.description || ''}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Unit Price <span class="text-danger">*</span></label>
                                <input type="number" name="items[${itemCounter}][unit_price]" 
                                       class="form-control item-input" value="${data.price || ''}" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="items[${itemCounter}][quantity]" 
                                       class="form-control item-input" value="${data.quantity || '1'}" 
                                       step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Unit Type</label>
                                <select name="items[${itemCounter}][unit_type]" class="form-control">
                                    <option value="piece" ${(data.unit || 'piece') === 'piece' ? 'selected' : ''}>Piece</option>
                                    <option value="hour" ${data.unit === 'hour' ? 'selected' : ''}>Hour</option>
                                    <option value="kg" ${data.unit === 'kg' ? 'selected' : ''}>Kilogram</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Line Total</label>
                                <input type="text" class="form-control line-total" readonly value="{{ $settings->formatCurrency(0) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="items[${itemCounter}][is_taxable]" 
                                       class="custom-control-input item-input" id="taxable_${itemCounter}" 
                                       ${data.taxable ? 'checked' : ''}>
                                <label class="custom-control-label" for="taxable_${itemCounter}">Taxable</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tax Rate (%)</label>
                                <input type="number" name="items[${itemCounter}][tax_rate]" 
                                       class="form-control item-input" value="${data.taxRate || ''}" 
                                       step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#invoice-items').append(itemHtml);
            itemCounter++;
            calculateTotals();
        }
        
        function addItemFromCatalog(option) {
            const data = {
                id: option.val(),
                name: option.data('name'),
                description: option.data('description'),
                price: option.data('price'),
                unit: option.data('unit'),
                taxable: option.data('taxable'),
                taxRate: option.data('tax-rate')
            };
            
            addInvoiceItem(data);
        }
        
        function calculateTotals() {
            let subtotal = 0;
            let totalTax = 0;
            
            $('.invoice-item').each(function() {
                const unitPrice = parseFloat($(this).find('input[name$="[unit_price]"]').val()) || 0;
                const quantity = parseFloat($(this).find('input[name$="[quantity]"]').val()) || 0;
                const lineTotal = unitPrice * quantity;
                
                const isTaxable = $(this).find('input[name$="[is_taxable]"]').is(':checked');
                const taxRate = parseFloat($(this).find('input[name$="[tax_rate]"]').val()) || 0;
                let itemTax = 0;
                
                if (isTaxable && taxRate > 0) {
                    itemTax = lineTotal * (taxRate / 100);
                }
                
                totalTax += itemTax;
                subtotal += lineTotal;
                
                $(this).find('.line-total').val(formatCurrency(lineTotal));
            });
            
            const total = subtotal + totalTax;
            
            $('#subtotal').text(formatCurrency(subtotal));
            $('#tax-amount').text(formatCurrency(totalTax));
            $('#total-amount').text(formatCurrency(total));
            
            // Enable preview button if form has data
            checkFormValidity();
        }
        
        function checkFormValidity() {
            const hasClient = $('#client_id').val();
            const hasItems = $('.invoice-item').length > 0;
            const hasValidItems = $('.invoice-item').toArray().some(item => {
                const name = $(item).find('input[name$="[item_name]"]').val();
                const price = $(item).find('input[name$="[unit_price]"]').val();
                const quantity = $(item).find('input[name$="[quantity]"]').val();
                return name && price && quantity;
            });
            
            if (hasClient && hasItems && hasValidItems) {
                $('#preview-invoice').prop('disabled', false);
            } else {
                $('#preview-invoice').prop('disabled', true);
            }
        }
        
        // Preview invoice functionality
        $('#preview-invoice').click(function() {
            // Create a temporary form to submit for preview
            const form = $('#invoice-form')[0];
            const formData = new FormData(form);
            
            // Add preview flag
            formData.append('preview', '1');
            
            // Submit form data to preview endpoint
            $.ajax({
                url: '{{ route("invoices.preview-form") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Open preview in new window
                    const previewWindow = window.open('', '_blank');
                    previewWindow.document.write(response);
                    previewWindow.document.close();
                },
                error: function() {
                    alert('Error generating preview. Please check your form data.');
                }
            });
        });
        
        // Check form validity on input changes
        $(document).on('input change', 'select, input', function() {
            checkFormValidity();
        });
    </script>
@stop