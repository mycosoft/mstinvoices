@extends('adminlte::page')

@section('title', 'Add New Domain')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Add New Domain</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('domains.index') }}">Domains</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Domain Information</h3>
                </div>
                
                <form action="{{ route('domains.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="domain_name">Domain Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('domain_name') is-invalid @enderror" 
                                           id="domain_name" 
                                           name="domain_name" 
                                           value="{{ old('domain_name') }}" 
                                           placeholder="example.com"
                                           required>
                                    @error('domain_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id">Client</label>
                                    <div class="input-group">
                                        <select class="form-control @error('client_id') is-invalid @enderror" 
                                                id="client_id" 
                                                name="client_id">
                                            <option value="">Select a client (optional)</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" 
                                                        {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#addClientModal" title="Add New Client">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_date">Registration Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('registration_date') is-invalid @enderror" 
                                           id="registration_date" 
                                           name="registration_date" 
                                           value="{{ old('registration_date', date('Y-m-d')) }}" 
                                           required>
                                    @error('registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('expiry_date') is-invalid @enderror" 
                                           id="expiry_date" 
                                           name="expiry_date" 
                                           value="{{ old('expiry_date', date('Y-m-d', strtotime('+1 year'))) }}" 
                                           required>
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registrar">Registrar <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('registrar') is-invalid @enderror" 
                                           id="registrar" 
                                           name="registrar" 
                                           value="{{ old('registrar', 'NameSilo') }}" 
                                           required>
                                    @error('registrar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email">Contact Email</label>
                                    <input type="email" 
                                           class="form-control @error('contact_email') is-invalid @enderror" 
                                           id="contact_email" 
                                           name="contact_email" 
                                           value="{{ old('contact_email') }}" 
                                           placeholder="admin@example.com">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_cost">Registration Cost</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('registration_cost') is-invalid @enderror" 
                                               id="registration_cost" 
                                               name="registration_cost" 
                                               value="{{ old('registration_cost') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                    </div>
                                    @error('registration_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="renewal_cost">Renewal Cost</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('renewal_cost') is-invalid @enderror" 
                                               id="renewal_cost" 
                                               name="renewal_cost" 
                                               value="{{ old('renewal_cost') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                    </div>
                                    @error('renewal_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="auto_renew" 
                                               name="auto_renew" 
                                               value="1" 
                                               {{ old('auto_renew') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_renew">
                                            Auto Renew
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="privacy_protection" 
                                               name="privacy_protection" 
                                               value="1" 
                                               {{ old('privacy_protection') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="privacy_protection">
                                            Privacy Protection
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Additional notes about this domain...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Domain
                        </button>
                        <a href="{{ route('domains.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Domain Registration via NameSilo</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        You can also register new domains directly through NameSilo API integration.
                    </p>
                    <div class="form-group">
                        <label for="register_domain">Domain to Register</label>
                        <input type="text" 
                               class="form-control" 
                               id="register_domain" 
                               placeholder="newdomain.com">
                    </div>
                    <button type="button" class="btn btn-success btn-block" onclick="checkAvailability()">
                        <i class="fas fa-search"></i> Check Availability
                    </button>
                    <div id="availability-result" class="mt-3" style="display: none;">
                        <!-- Availability result will be shown here -->
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Supported TLDs</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(array_chunk($supportedTlds, 2) as $tldChunk)
                            <div class="col-6">
                                @foreach($tldChunk as $tld)
                                    <small class="badge badge-light mr-1 mb-1">{{ $tld }}</small>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
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
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
<script>
function checkAvailability() {
    const domain = document.getElementById('register_domain').value;
    const resultDiv = document.getElementById('availability-result');
    
    if (!domain) {
        alert('Please enter a domain name');
        return;
    }
    
    resultDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Checking availability...</div>';
    resultDiv.style.display = 'block';
    
    fetch('{{ route("domains.check-availability") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ domain: domain })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            resultDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        } else {
            const available = data.available;
            const price = data.price;
            const message = data.message;
            
            let html = '<div class="alert alert-' + (available ? 'success' : 'danger') + '">';
            html += '<strong>' + (available ? 'Available!' : 'Not Available') + '</strong><br>';
            html += message;
            if (available && price) {
                html += '<br><strong>Price: $' + price + '</strong>';
            }
            html += '</div>';
            
            if (available) {
                html += '<button type="button" class="btn btn-primary btn-sm" onclick="registerDomain(\'' + domain + '\')">Register Domain</button>';
            }
            
            resultDiv.innerHTML = html;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger">Error checking availability: ' + error.message + '</div>';
    });
}

function registerDomain(domain) {
    if (confirm('Are you sure you want to register ' + domain + '? This will use your NameSilo account.')) {
        // Create a form to register the domain
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("domains.register") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const domainInput = document.createElement('input');
        domainInput.type = 'hidden';
        domainInput.name = 'domain_name';
        domainInput.value = domain;
        
        const yearsInput = document.createElement('input');
        yearsInput.type = 'hidden';
        yearsInput.name = 'years';
        yearsInput.value = '1';
        
        form.appendChild(csrfToken);
        form.appendChild(domainInput);
        form.appendChild(yearsInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

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
            if (typeof toastr !== 'undefined') {
                toastr.success('Client created successfully!');
            } else {
                alert('Client created successfully!');
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
            submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Client');
        }
    });
});

// Reset form when modal is closed
$('#addClientModal').on('hidden.bs.modal', function() {
    $('#addClientForm')[0].reset();
    $('#clientFormErrors').addClass('d-none');
});
</script>
@stop
