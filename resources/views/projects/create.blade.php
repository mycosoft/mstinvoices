@extends('adminlte::page')

@section('title', 'Create Project')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create New Project</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Project Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   required value="{{ old('name') }}" placeholder="Enter project name">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id">Client</label>
                            <div class="input-group">
                                <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror">
                                    <option value="">Select client (optional)</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @selected(old('client_id')==$client->id)>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#addClientModal" title="Add New Client">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                @foreach(['pending', 'in_progress', 'completed', 'cancelled'] as $st)
                                    <option value="{{ $st }}" @selected(old('status', 'pending')===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date') }}">
                            @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date') }}">
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="budget">Budget</label>
                            <input type="number" step="0.01" min="0" name="budget" id="budget" 
                                   class="form-control @error('budget') is-invalid @enderror" 
                                   value="{{ old('budget') }}" placeholder="0.00">
                            @error('budget')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror">
                                <option value="UGX" @selected(old('currency', $settings->default_currency) == 'UGX')>UGX - Ugandan Shilling</option>
                                <option value="USD" @selected(old('currency', $settings->default_currency) == 'USD')>USD - US Dollar</option>
                                <option value="EUR" @selected(old('currency') == 'EUR')>EUR - Euro</option>
                                <option value="GBP" @selected(old('currency') == 'GBP')>GBP - British Pound</option>
                            </select>
                            @error('currency')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" placeholder="Enter project description">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save"></i> Create Project
                    </button>
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="clientFormErrors"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_name" name="name" required placeholder="Enter full name">
                            </div>
                            <div class="form-group">
                                <label for="modal_company_name">Company Name</label>
                                <input type="text" class="form-control" id="modal_company_name" name="company_name" placeholder="Enter company name">
                            </div>
                            <div class="form-group">
                                <label for="modal_email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="modal_email" name="email" required placeholder="Enter email address">
                            </div>
                            <div class="form-group">
                                <label for="modal_phone">Phone Number</label>
                                <input type="text" class="form-control" id="modal_phone" name="phone" placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_address">Street Address</label>
                                <textarea class="form-control" id="modal_address" name="address" rows="2" placeholder="Enter street address"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="modal_city">City</label>
                                <input type="text" class="form-control" id="modal_city" name="city" placeholder="Enter city">
                            </div>
                            <div class="form-group">
                                <label for="modal_country">Country</label>
                                <input type="text" class="form-control" id="modal_country" name="country" placeholder="Enter country" value="Uganda">
                            </div>
                            <div class="form-group">
                                <label for="modal_tax_number">Tax Number</label>
                                <input type="text" class="form-control" id="modal_tax_number" name="tax_number" placeholder="Enter tax number">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveClientBtn">
                        <i class="fas fa-save"></i> Create Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle modal client form submission via AJAX
    $('#addClientForm').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = $('#saveClientBtn');
        var errorAlert = $('#clientFormErrors');
        
        // Reset error display
        errorAlert.addClass('d-none').html('');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Add new client to dropdown
                var newOption = new Option(response.client.name, response.client.id, true, true);
                $('#client_id').append(newOption).val(response.client.id);
                
                // Close modal and reset form
                $('#addClientModal').modal('hide');
                form[0].reset();
                
                // Show success message
                toastr.success('Client created successfully!');
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
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Client');
            }
        });
    });
    
    // Reset form when modal is closed
    $('#addClientModal').on('hidden.bs.modal', function() {
        $('#addClientForm')[0].reset();
        $('#clientFormErrors').addClass('d-none');
    });
});
</script>
@stop
