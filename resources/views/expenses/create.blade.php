@extends('adminlte::page')

@section('title', 'Create Expense')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create New Expense</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('expenses.store') }}" method="POST" id="expense-form" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Main Expense Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Expense Information</h3>
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

                            <!-- Basic Expense Details -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_number">Expense Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('expense_number') is-invalid @enderror" 
                                               id="expense_number" name="expense_number" 
                                               value="{{ old('expense_number', $expenseNumber) }}" required>
                                        @error('expense_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference_number">Reference Number</label>
                                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                               id="reference_number" name="reference_number" 
                                               value="{{ old('reference_number') }}" placeholder="Receipt/Invoice number">
                                        @error('reference_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="title">Expense Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" 
                                               value="{{ old('title') }}" required placeholder="Enter expense title">
                                        @error('title')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3" 
                                                  placeholder="Detailed description of the expense">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category <span class="text-danger">*</span></label>
                                        <select class="form-control @error('category') is-invalid @enderror" 
                                                id="category" name="category" required>
                                            <option value="">Select a category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Client (Optional)</label>
                                        <select class="form-control @error('client_id') is-invalid @enderror" 
                                                id="client_id" name="client_id">
                                            <option value="">Not associated with a client</option>
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
                                        <label for="project_id">Project (Optional)</label>
                                        <select class="form-control @error('project_id') is-invalid @enderror" 
                                                id="project_id" name="project_id">
                                            <option value="">Not associated with a project</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Vendor Information -->
                            <h5 class="mt-4 mb-3">Vendor Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vendor_name">Vendor Name</label>
                                        <input type="text" class="form-control @error('vendor_name') is-invalid @enderror" 
                                               id="vendor_name" name="vendor_name" 
                                               value="{{ old('vendor_name') }}" placeholder="Vendor or supplier name">
                                        @error('vendor_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vendor_email">Vendor Email</label>
                                        <input type="email" class="form-control @error('vendor_email') is-invalid @enderror" 
                                               id="vendor_email" name="vendor_email" 
                                               value="{{ old('vendor_email') }}" placeholder="vendor@example.com">
                                        @error('vendor_email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vendor_phone">Vendor Phone</label>
                                        <input type="text" class="form-control @error('vendor_phone') is-invalid @enderror" 
                                               id="vendor_phone" name="vendor_phone" 
                                               value="{{ old('vendor_phone') }}" placeholder="Phone number">
                                        @error('vendor_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <h5 class="mt-4 mb-3">Financial Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="net_amount">Net Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('net_amount') is-invalid @enderror" 
                                               id="net_amount" name="net_amount" 
                                               value="{{ old('net_amount') }}" required>
                                        @error('net_amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tax_rate">Tax Rate (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control @error('tax_rate') is-invalid @enderror" 
                                               id="tax_rate" name="tax_rate" 
                                               value="{{ old('tax_rate', $settings->default_expense_tax_rate) }}">
                                        @error('tax_rate')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount">Total Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" 
                                               value="{{ old('amount') }}" required readonly>
                                        @error('amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency">Currency <span class="text-danger">*</span></label>
                                        <select class="form-control @error('currency') is-invalid @enderror" 
                                                id="currency" name="currency" required>
                                            <option value="UGX" {{ old('currency', $settings->default_currency) == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                                            <option value="USD" {{ old('currency', $settings->default_currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        </select>
                                        @error('currency')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="receipt_number">Receipt Number</label>
                                        <input type="text" class="form-control @error('receipt_number') is-invalid @enderror" 
                                               id="receipt_number" name="receipt_number" 
                                               value="{{ old('receipt_number') }}" placeholder="Receipt or transaction number">
                                        @error('receipt_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <h5 class="mt-4 mb-3">Dates</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                               id="expense_date" name="expense_date" 
                                               value="{{ old('expense_date', today()->format('Y-m-d')) }}" required>
                                        @error('expense_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                               id="due_date" name="due_date" 
                                               value="{{ old('due_date') }}">
                                        @error('due_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Status and Options -->
                            <h5 class="mt-4 mb-3">Status & Options</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment_status">Payment Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('payment_status') is-invalid @enderror" 
                                                id="payment_status" name="payment_status" required>
                                            <option value="unpaid" {{ old('payment_status', 'unpaid') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                        </select>
                                        @error('payment_status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method</label>
                                        <select class="form-control @error('payment_method') is-invalid @enderror" 
                                                id="payment_method" name="payment_method">
                                            <option value="">Select payment method</option>
                                            @foreach($paymentMethods as $key => $method)
                                                <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>
                                                    {{ $method }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('payment_method')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Checkboxes -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox d-inline-block mr-4">
                                            <input type="checkbox" class="custom-control-input" id="requires_approval" 
                                                   name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="requires_approval">Requires Approval</label>
                                        </div>
                                        <div class="custom-control custom-checkbox d-inline-block mr-4">
                                            <input type="checkbox" class="custom-control-input" id="is_billable" 
                                                   name="is_billable" value="1" {{ old('is_billable') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_billable">Billable to Client</label>
                                        </div>
                                        <div class="custom-control custom-checkbox d-inline-block mr-4">
                                            <input type="checkbox" class="custom-control-input" id="is_reimbursable" 
                                                   name="is_reimbursable" value="1" {{ old('is_reimbursable') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_reimbursable">Reimbursable</label>
                                        </div>
                                        <div class="custom-control custom-checkbox d-inline-block">
                                            <input type="checkbox" class="custom-control-input" id="is_recurring" 
                                                   name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_recurring">Recurring Expense</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recurring Options (Hidden by default) -->
                            <div id="recurring-options" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recurring_frequency">Recurring Frequency</label>
                                            <select class="form-control" id="recurring_frequency" name="recurring_frequency">
                                                <option value="monthly" {{ old('recurring_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="quarterly" {{ old('recurring_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                <option value="yearly" {{ old('recurring_frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recurring_end_date">Recurring End Date</label>
                                            <input type="date" class="form-control" id="recurring_end_date" 
                                                   name="recurring_end_date" value="{{ old('recurring_end_date') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <h5 class="mt-4 mb-3">Additional Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="project_code">Project Code</label>
                                        <input type="text" class="form-control @error('project_code') is-invalid @enderror" 
                                               id="project_code" name="project_code" 
                                               value="{{ old('project_code') }}" placeholder="Project or job code">
                                        @error('project_code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="department">Department</label>
                                        <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                               id="department" name="department" 
                                               value="{{ old('department') }}" placeholder="Department or division">
                                        @error('department')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" 
                                               value="{{ old('location') }}" placeholder="Location or office">
                                        @error('location')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="3" 
                                                  placeholder="Internal notes and additional information">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Attachments -->
                            <h5 class="mt-4 mb-3">Attachments</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="attachments">Upload Receipts/Invoices</label>
                                        <input type="file" class="form-control-file @error('attachments.*') is-invalid @enderror" 
                                               id="attachments" name="attachments[]" multiple 
                                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                        <small class="form-text text-muted">
                                            Supported formats: JPG, PNG, PDF, DOC, DOCX. Maximum file size: 5MB each.
                                        </small>
                                        @error('attachments.*')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Expense
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Expense Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Expense Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Net Amount:</strong></td>
                                                <td class="text-right" id="summary-net-amount">{{ $settings->currency_symbol }} 0.00</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tax Amount:</strong></td>
                                                <td class="text-right" id="summary-tax-amount">{{ $settings->currency_symbol }} 0.00</td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td><strong>Total Amount:</strong></td>
                                                <td class="text-right" id="summary-total-amount"><strong>{{ $settings->currency_symbol }} 0.00</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group-vertical btn-block">
                                <button type="button" class="btn btn-info btn-sm" onclick="calculateTotals()">
                                    <i class="fas fa-calculator"></i> Calculate Totals
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="clearForm()">
                                    <i class="fas fa-eraser"></i> Clear Form
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .required {
            color: #dc3545;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Calculate totals when amounts change
            $('#net_amount, #tax_rate').on('input', function() {
                calculateTotals();
            });

            // Show/hide recurring options
            $('#is_recurring').change(function() {
                if ($(this).is(':checked')) {
                    $('#recurring-options').slideDown();
                } else {
                    $('#recurring-options').slideUp();
                }
            });

            // Initialize calculation
            calculateTotals();
        });

        function calculateTotals() {
            var netAmount = parseFloat($('#net_amount').val()) || 0;
            var taxRate = parseFloat($('#tax_rate').val()) || 0;
            
            var taxAmount = netAmount * (taxRate / 100);
            var totalAmount = netAmount + taxAmount;
            
            $('#amount').val(totalAmount.toFixed(2));
            
            // Update summary
            var currencySymbol = '{{ $settings->currency_symbol }}';
            $('#summary-net-amount').text(currencySymbol + ' ' + netAmount.toFixed(2));
            $('#summary-tax-amount').text(currencySymbol + ' ' + taxAmount.toFixed(2));
            $('#summary-total-amount').html('<strong>' + currencySymbol + ' ' + totalAmount.toFixed(2) + '</strong>');
        }

        function clearForm() {
            if (confirm('Are you sure you want to clear the form? All data will be lost.')) {
                $('#expense-form')[0].reset();
                $('#recurring-options').hide();
                calculateTotals();
            }
        }
    </script>
@stop