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
                                        <label for="project_id">Project</label>
                                        <div class="input-group">
                                            <select class="form-control @error('project_id') is-invalid @enderror" 
                                                    id="project_id" name="project_id">
                                                <option value="">Select a project (optional)</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                        {{ $project->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#addProjectModal" title="Add New Project">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('project_id')
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
                                            <option value="UGX" {{ old('currency', 'UGX') == 'UGX' ? 'selected' : '' }}>UGX</option>
                                            <option value="USD" {{ old('currency', 'UGX') == 'USD' ? 'selected' : '' }}>USD</option>
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

    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Add New Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addProjectForm" method="POST" action="{{ route('projects.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" id="projectFormErrors"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_project_name">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="modal_project_name" name="name" required placeholder="Enter project name">
                                </div>
                                <div class="form-group">
                                    <label for="modal_project_client">Client</label>
                                    <select class="form-control" id="modal_project_client" name="client_id">
                                        <option value="">Select client (optional)</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="modal_project_status">Status</label>
                                    <select class="form-control" id="modal_project_status" name="status">
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_project_start_date">Start Date</label>
                                    <input type="date" class="form-control" id="modal_project_start_date" name="start_date">
                                </div>
                                <div class="form-group">
                                    <label for="modal_project_end_date">End Date</label>
                                    <input type="date" class="form-control" id="modal_project_end_date" name="end_date">
                                </div>
                                <div class="form-group">
                                    <label for="modal_project_budget">Budget</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="modal_project_budget" name="budget" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="modal_project_description">Description</label>
                                    <textarea class="form-control" id="modal_project_description" name="description" rows="3" placeholder="Enter project description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveProjectBtn">
                            <i class="fas fa-save"></i> Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
        // Ensure jQuery is loaded before running the script
        function initInvoiceCreate() {
            if (typeof $ === 'undefined') {
                console.log('jQuery not loaded yet, retrying in 100ms...');
                setTimeout(initInvoiceCreate, 100);
                return;
            }
            
            console.log('jQuery loaded successfully for invoice create');
            
            let itemCounter = 0;
            
            // Currency settings from server
            const currencySymbol = '{{ $settings->currency_symbol }}';
            const currencyPosition = '{{ $settings->currency_position }}';
            
            function formatCurrency(amount) {
                const currency = $('#currency').val() || 'UGX';
                return currency + ' ' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
            
            $(document).ready(function() {
            addInvoiceItem();
            
            $('#add-item').click(function() {
                addInvoiceItem();
            });
            
            // Add from catalog button functionality
            $('#add-from-catalog').click(function() {
                // Scroll to the catalog section and highlight it
                $('html, body').animate({
                    scrollTop: $('#catalog-items').offset().top - 100
                }, 500);
                $('#catalog-items').focus().addClass('border-primary');
                setTimeout(() => {
                    $('#catalog-items').removeClass('border-primary');
                }, 2000);
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
                                <textarea name="items[${itemCounter}][item_description]" 
                                          class="form-control" rows="2" 
                                          placeholder="Item description...">${data.description || ''}</textarea>
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
            }
        });
        }
        
        // Start the initialization
        initInvoiceCreate();

        // Handle modal project form submission via AJAX
        $('#addProjectForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = $('#saveProjectBtn');
            var errorAlert = $('#projectFormErrors');
            
            // Reset error display
            errorAlert.addClass('d-none').html('');
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Add new project to dropdown
                    var newOption = new Option(response.project.name, response.project.id, true, true);
                    $('#project_id').append(newOption).val(response.project.id);
                    
                    // Close modal and reset form
                    $('#addProjectModal').modal('hide');
                    form[0].reset();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Project created successfully!');
                    } else {
                        alert('Project created successfully!');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        var errorHtml = '<ul class="mb-0">';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value + '</li>';
                        });
                        errorHtml += '</ul>';
                        errorAlert.removeClass('d-none').html(errorHtml);
                    } else {
                        errorAlert.removeClass('d-none').html('<ul class="mb-0"><li>An error occurred. Please try again.</li></ul>');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Project');
                }
            });
        });
        
        // Reset form when modal is closed
        $('#addProjectModal').on('hidden.bs.modal', function() {
            $('#addProjectForm')[0].reset();
            $('#projectFormErrors').addClass('d-none');
        });
    </script>
@stop