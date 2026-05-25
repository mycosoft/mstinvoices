@extends('adminlte::page')

@section('title', 'Edit Domain - ' . $domain->domain_name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit Domain</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('domains.index') }}">Domains</a></li>
                <li class="breadcrumb-item"><a href="{{ route('domains.show', $domain) }}">{{ $domain->domain_name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Domain Information</h3>
                </div>
                
                <form action="{{ route('domains.update', $domain) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="domain_name">Domain Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="domain_name" 
                                           value="{{ $domain->domain_name }}" 
                                           readonly>
                                    <small class="form-text text-muted">Domain name cannot be changed after creation.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id">Client</label>
                                    <select class="form-control @error('client_id') is-invalid @enderror" 
                                            id="client_id" 
                                            name="client_id">
                                        <option value="">Select a client (optional)</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                    {{ old('client_id', $domain->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="active" {{ old('status', $domain->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="expired" {{ old('status', $domain->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                        <option value="suspended" {{ old('status', $domain->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="pending" {{ old('status', $domain->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registrar">Registrar <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('registrar') is-invalid @enderror" 
                                           id="registrar" 
                                           name="registrar" 
                                           value="{{ old('registrar', $domain->registrar) }}" 
                                           required>
                                    @error('registrar')
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
                                           value="{{ old('registration_date', $domain->registration_date->format('Y-m-d')) }}" 
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
                                           value="{{ old('expiry_date', $domain->expiry_date->format('Y-m-d')) }}" 
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
                                    <label for="renewal_date">Renewal Date</label>
                                    <input type="date" 
                                           class="form-control @error('renewal_date') is-invalid @enderror" 
                                           id="renewal_date" 
                                           name="renewal_date" 
                                           value="{{ old('renewal_date', $domain->renewal_date ? $domain->renewal_date->format('Y-m-d') : '') }}">
                                    @error('renewal_date')
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
                                           value="{{ old('contact_email', $domain->contact_email) }}" 
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
                                               value="{{ old('registration_cost', $domain->registration_cost) }}" 
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
                                               value="{{ old('renewal_cost', $domain->renewal_cost) }}" 
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
                                               {{ old('auto_renew', $domain->auto_renew) ? 'checked' : '' }}>
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
                                               {{ old('privacy_protection', $domain->privacy_protection) ? 'checked' : '' }}>
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
                                      placeholder="Additional notes about this domain...">{{ old('notes', $domain->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Domain
                        </button>
                        <a href="{{ route('domains.show', $domain) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Domain Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Domain Info</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Domain:</strong></td>
                            <td>{{ $domain->domain_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge {{ $domain->status_badge_class }}">
                                    {{ ucfirst($domain->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Expiry:</strong></td>
                            <td>{{ $domain->expiry_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Days Left:</strong></td>
                            <td>
                                @if($domain->isExpired())
                                    <span class="text-danger">Expired</span>
                                @else
                                    <span class="text-{{ $domain->isExpiringSoon(30) ? 'warning' : 'success' }}">
                                        {{ $domain->days_until_expiry }} days
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('domains.show', $domain) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-eye"></i> View Domain
                    </a>
                    
                    @if(!$domain->isExpired())
                        <button type="button" class="btn btn-success btn-block mb-2" data-toggle="modal" data-target="#renewModal">
                            <i class="fas fa-redo"></i> Renew Domain
                        </button>
                    @endif
                    
                    <form action="{{ route('domains.sync', $domain) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-sync"></i> Sync with NameSilo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cost Summary Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cost Summary</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Registration:</strong></td>
                            <td>{{ $domain->registration_cost ? '$' . number_format($domain->registration_cost, 2) : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Renewal:</strong></td>
                            <td>{{ $domain->renewal_cost ? '$' . number_format($domain->renewal_cost, 2) : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total:</strong></td>
                            <td>
                                @php
                                    $totalCost = ($domain->registration_cost ?: 0) + ($domain->renewal_cost ?: 0);
                                @endphp
                                ${{ number_format($totalCost, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Renew Domain Modal -->
    <div class="modal fade" id="renewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('domains.renew', $domain) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Renew Domain</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="years">Years to Renew</label>
                            <select class="form-control" id="years" name="years" required>
                                <option value="1">1 Year</option>
                                <option value="2">2 Years</option>
                                <option value="3">3 Years</option>
                                <option value="4">4 Years</option>
                                <option value="5">5 Years</option>
                            </select>
                        </div>
                        <p class="text-muted">
                            This will renew {{ $domain->domain_name }} through NameSilo API.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Renew Domain</button>
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
    {{-- Add here extra JavaScript --}}
@stop
