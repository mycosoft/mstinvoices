@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Settings</h1>
@stop

@section('content')
<!-- Display Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> Please fix the following errors:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Company Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Company Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name', $settings->company_name) }}" required>
                                @error('company_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_email">Company Email</label>
                                <input type="email" class="form-control @error('company_email') is-invalid @enderror" 
                                       id="company_email" name="company_email" value="{{ old('company_email', $settings->company_email) }}">
                                @error('company_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_phone">Company Phone</label>
                                <input type="text" class="form-control @error('company_phone') is-invalid @enderror" 
                                       id="company_phone" name="company_phone" value="{{ old('company_phone', $settings->company_phone) }}">
                                @error('company_phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_website">Company Website</label>
                                <input type="url" class="form-control @error('company_website') is-invalid @enderror" 
                                       id="company_website" name="company_website" value="{{ old('company_website', $settings->company_website) }}" 
                                       placeholder="https://example.com">
                                @error('company_website')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="company_address">Company Address</label>
                                <textarea class="form-control @error('company_address') is-invalid @enderror" 
                                          id="company_address" name="company_address" rows="2">{{ old('company_address', $settings->company_address) }}</textarea>
                                @error('company_address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="company_city">City</label>
                                <input type="text" class="form-control @error('company_city') is-invalid @enderror" 
                                       id="company_city" name="company_city" value="{{ old('company_city', $settings->company_city) }}">
                                @error('company_city')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="company_state">State/Province</label>
                                <input type="text" class="form-control @error('company_state') is-invalid @enderror" 
                                       id="company_state" name="company_state" value="{{ old('company_state', $settings->company_state) }}">
                                @error('company_state')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="company_postal_code">Postal Code</label>
                                <input type="text" class="form-control @error('company_postal_code') is-invalid @enderror" 
                                       id="company_postal_code" name="company_postal_code" value="{{ old('company_postal_code', $settings->company_postal_code) }}">
                                @error('company_postal_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="company_country">Country</label>
                                <input type="text" class="form-control @error('company_country') is-invalid @enderror" 
                                       id="company_country" name="company_country" value="{{ old('company_country', $settings->company_country) }}">
                                @error('company_country')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_tax_number">Tax Number</label>
                                <input type="text" class="form-control @error('company_tax_number') is-invalid @enderror" 
                                       id="company_tax_number" name="company_tax_number" value="{{ old('company_tax_number', $settings->company_tax_number) }}">
                                @error('company_tax_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_registration_number">Registration Number</label>
                                <input type="text" class="form-control @error('company_registration_number') is-invalid @enderror" 
                                       id="company_registration_number" name="company_registration_number" value="{{ old('company_registration_number', $settings->company_registration_number) }}">
                                @error('company_registration_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_logo">Company Logo</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('company_logo') is-invalid @enderror" 
                                               id="company_logo" name="company_logo" accept="image/*">
                                        <label class="custom-file-label" for="company_logo">Choose logo...</label>
                                    </div>
                                </div>
                                @error('company_logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($settings->company_logo_path)
                                <div class="current-logo">
                                    <label>Current Logo:</label><br>
                                    <img src="{{ asset('storage/' . $settings->company_logo_path) }}" alt="Company Logo" 
                                         style="max-height: 80px; max-width: 200px;" class="img-thumbnail">
                                    <br>
                                    <a href="{{ route('settings.remove-logo') }}" class="btn btn-sm btn-danger mt-2"
                                       onclick="event.preventDefault(); if(confirm('Remove logo?')) { document.getElementById('remove-logo-form').submit(); }">
                                        <i class="fas fa-trash"></i> Remove Logo
                                    </a>
                                    <form id="remove-logo-form" action="{{ route('settings.remove-logo') }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Invoice Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice"></i> Invoice Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="default_currency">Default Currency <span class="text-danger">*</span></label>
                                <select class="form-control @error('default_currency') is-invalid @enderror" 
                                        id="default_currency" name="default_currency" required>
                                    <option value="USD" {{ old('default_currency', $settings->default_currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="UGX" {{ old('default_currency', $settings->default_currency) == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                                    <option value="EUR" {{ old('default_currency', $settings->default_currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('default_currency', $settings->default_currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    <option value="CAD" {{ old('default_currency', $settings->default_currency) == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                    <option value="AUD" {{ old('default_currency', $settings->default_currency) == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                    <option value="JPY" {{ old('default_currency', $settings->default_currency) == 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                                </select>
                                @error('default_currency')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_symbol">Currency Symbol <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('currency_symbol') is-invalid @enderror" 
                                       id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings->currency_symbol) }}" required>
                                @error('currency_symbol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_position">Currency Position <span class="text-danger">*</span></label>
                                <select class="form-control @error('currency_position') is-invalid @enderror" 
                                        id="currency_position" name="currency_position" required>
                                    <option value="before" {{ old('currency_position', $settings->currency_position) == 'before' ? 'selected' : '' }}>Before Amount ($100)</option>
                                    <option value="after" {{ old('currency_position', $settings->currency_position) == 'after' ? 'selected' : '' }}>After Amount (100$)</option>
                                </select>
                                @error('currency_position')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="invoice_prefix">Invoice Prefix <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('invoice_prefix') is-invalid @enderror" 
                                       id="invoice_prefix" name="invoice_prefix" value="{{ old('invoice_prefix', $settings->invoice_prefix) }}" required>
                                @error('invoice_prefix')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Example: INV, BILL, etc.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="invoice_number_length">Invoice Number Length <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('invoice_number_length') is-invalid @enderror" 
                                       id="invoice_number_length" name="invoice_number_length" 
                                       value="{{ old('invoice_number_length', $settings->invoice_number_length) }}" min="3" max="10" required>
                                @error('invoice_number_length')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Number of digits (3-10)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="default_payment_terms">Default Payment Terms (Days) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('default_payment_terms') is-invalid @enderror" 
                                       id="default_payment_terms" name="default_payment_terms" 
                                       value="{{ old('default_payment_terms', $settings->default_payment_terms) }}" min="1" max="365" required>
                                @error('default_payment_terms')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="default_tax_rate">Default Tax Rate (%)</label>
                                <input type="number" class="form-control @error('default_tax_rate') is-invalid @enderror" 
                                       id="default_tax_rate" name="default_tax_rate" 
                                       value="{{ old('default_tax_rate', $settings->default_tax_rate) }}" 
                                       min="0" max="100" step="0.01">
                                @error('default_tax_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="default_invoice_notes">Default Invoice Notes</label>
                                <textarea class="form-control @error('default_invoice_notes') is-invalid @enderror" 
                                          id="default_invoice_notes" name="default_invoice_notes" rows="3">{{ old('default_invoice_notes', $settings->default_invoice_notes) }}</textarea>
                                @error('default_invoice_notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="default_terms_conditions">Default Terms & Conditions</label>
                                <textarea class="form-control @error('default_terms_conditions') is-invalid @enderror" 
                                          id="default_terms_conditions" name="default_terms_conditions" rows="4">{{ old('default_terms_conditions', $settings->default_terms_conditions) }}</textarea>
                                @error('default_terms_conditions')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="default_invoice_footer">Default Invoice Footer</label>
                                <textarea class="form-control @error('default_invoice_footer') is-invalid @enderror" 
                                          id="default_invoice_footer" name="default_invoice_footer" rows="2">{{ old('default_invoice_footer', $settings->default_invoice_footer) }}</textarea>
                                @error('default_invoice_footer')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> System Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_format">Date Format <span class="text-danger">*</span></label>
                                <select class="form-control @error('date_format') is-invalid @enderror" 
                                        id="date_format" name="date_format" required>
                                    <option value="Y-m-d" {{ old('date_format', $settings->date_format) == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD ({{ date('Y-m-d') }})</option>
                                    <option value="m/d/Y" {{ old('date_format', $settings->date_format) == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY ({{ date('m/d/Y') }})</option>
                                    <option value="d/m/Y" {{ old('date_format', $settings->date_format) == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY ({{ date('d/m/Y') }})</option>
                                    <option value="M d, Y" {{ old('date_format', $settings->date_format) == 'M d, Y' ? 'selected' : '' }}>Month DD, YYYY ({{ date('M d, Y') }})</option>
                                </select>
                                @error('date_format')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="time_format">Time Format <span class="text-danger">*</span></label>
                                <select class="form-control @error('time_format') is-invalid @enderror" 
                                        id="time_format" name="time_format" required>
                                    <option value="H:i" {{ old('time_format', $settings->time_format) == 'H:i' ? 'selected' : '' }}>24 Hour ({{ date('H:i') }})</option>
                                    <option value="g:i A" {{ old('time_format', $settings->time_format) == 'g:i A' ? 'selected' : '' }}>12 Hour ({{ date('g:i A') }})</option>
                                </select>
                                @error('time_format')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="timezone">Timezone <span class="text-danger">*</span></label>
                                <select class="form-control @error('timezone') is-invalid @enderror" 
                                        id="timezone" name="timezone" required>
                                    <option value="UTC" {{ old('timezone', $settings->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ old('timezone', $settings->timezone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                    <option value="America/Chicago" {{ old('timezone', $settings->timezone) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                    <option value="America/Denver" {{ old('timezone', $settings->timezone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                    <option value="America/Los_Angeles" {{ old('timezone', $settings->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                    <option value="Europe/London" {{ old('timezone', $settings->timezone) == 'Europe/London' ? 'selected' : '' }}>London</option>
                                    <option value="Europe/Paris" {{ old('timezone', $settings->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                    <option value="Asia/Tokyo" {{ old('timezone', $settings->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                                </select>
                                @error('timezone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="language">Language</label>
                                <select class="form-control @error('language') is-invalid @enderror" 
                                        id="language" name="language">
                                    <option value="en" {{ old('language', $settings->language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="fr" {{ old('language', $settings->language) == 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="es" {{ old('language', $settings->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                                </select>
                                @error('language')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="auto_send_invoices" 
                                           name="auto_send_invoices" value="1" 
                                           {{ old('auto_send_invoices', $settings->auto_send_invoices) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_send_invoices">
                                        Auto-send invoices when created
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="auto_reminder_enabled" 
                                           name="auto_reminder_enabled" value="1" 
                                           {{ old('auto_reminder_enabled', $settings->auto_reminder_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_reminder_enabled">
                                        Enable automatic payment reminders
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
// Custom file input label update
$('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
});

// Currency symbol auto-update based on currency selection
$('#default_currency').on('change', function() {
    const currencySymbols = {
        'USD': '$',
        'UGX': 'UGX',
        'EUR': '€',
        'GBP': '£',
        'CAD': '$',
        'AUD': '$',
        'JPY': '¥'
    };
    
    const selectedCurrency = $(this).val();
    if (currencySymbols[selectedCurrency]) {
        $('#currency_symbol').val(currencySymbols[selectedCurrency]);
    }
});
</script>
@stop