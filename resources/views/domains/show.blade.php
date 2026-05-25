@extends('adminlte::page')

@section('title', 'Domain Details - ' . $domain->domain_name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Domain Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('domains.index') }}">Domains</a></li>
                <li class="breadcrumb-item active">{{ $domain->domain_name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Domain Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe"></i> {{ $domain->domain_name }}
                        <span class="badge {{ $domain->status_badge_class }} ml-2">{{ ucfirst($domain->status) }}</span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('domains.edit', $domain) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('domains.sync', $domain) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm" title="Sync with NameSilo">
                                <i class="fas fa-sync"></i> Sync
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Domain Name:</strong></td>
                                    <td>{{ $domain->domain_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registrar:</strong></td>
                                    <td>{{ $domain->registrar }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registration Date:</strong></td>
                                    <td>{{ $domain->registration_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Expiry Date:</strong></td>
                                    <td>
                                        {{ $domain->expiry_date->format('M d, Y') }}
                                        @if($domain->isExpired())
                                            <span class="badge badge-danger ml-2">Expired</span>
                                        @elseif($domain->isExpiringSoon(30))
                                            <span class="badge badge-warning ml-2">Expiring Soon</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Days Until Expiry:</strong></td>
                                    <td>
                                        @if($domain->isExpired())
                                            <span class="text-danger">Expired {{ abs($domain->days_until_expiry) }} days ago</span>
                                        @else
                                            <span class="text-{{ $domain->isExpiringSoon(30) ? 'warning' : 'success' }}">
                                                {{ $domain->days_until_expiry }} days
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Client:</strong></td>
                                    <td>
                                        @if($domain->client)
                                            <a href="{{ route('clients.show', $domain->client) }}">
                                                {{ $domain->client->display_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">No client assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Contact Email:</strong></td>
                                    <td>{{ $domain->contact_email ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Auto Renew:</strong></td>
                                    <td>
                                        @if($domain->auto_renew)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Privacy Protection:</strong></td>
                                    <td>
                                        @if($domain->privacy_protection)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Registration Cost:</strong></td>
                                    <td>{{ $domain->registration_cost ? '$' . number_format($domain->registration_cost, 2) : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Renewal Cost:</strong></td>
                                    <td>{{ $domain->renewal_cost ? '$' . number_format($domain->renewal_cost, 2) : 'Not set' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($domain->notes)
                        <div class="mt-3">
                            <h5>Notes</h5>
                            <p class="text-muted">{{ is_string($domain->notes) ? $domain->notes : json_encode($domain->notes) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- DNS Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-server"></i> DNS Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Nameservers</h6>
                            @if($domain->nameservers && is_array($domain->nameservers) && count($domain->nameservers) > 0)
                                <ul class="list-unstyled">
                                    @foreach($domain->nameservers as $nameserver)
                                        <li><code>{{ $nameserver }}</code></li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No nameservers set</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>DNS Records</h6>
                            @if($domain->dns_records && is_array($domain->dns_records) && count($domain->dns_records) > 0)
                                <ul class="list-unstyled">
                                    @foreach($domain->dns_records as $record)
                                        <li><code>{{ $record }}</code></li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No DNS records available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Data Card -->
            @if($apiData && isset($apiData['success']) && $apiData['success'])
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cloud"></i> Live Data from NameSilo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td><span class="badge badge-info">{{ $apiData['status'] ?? 'Unknown' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Expires:</strong></td>
                                        <td>{{ isset($apiData['expires']) ? \Carbon\Carbon::parse($apiData['expires'])->format('M d, Y') : 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ isset($apiData['created']) ? \Carbon\Carbon::parse($apiData['created'])->format('M d, Y') : 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated:</strong></td>
                                        <td>{{ isset($apiData['updated']) ? \Carbon\Carbon::parse($apiData['updated'])->format('M d, Y') : 'Unknown' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Locked:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ ($apiData['locked'] ?? 'no') === 'yes' ? 'success' : 'secondary' }}">
                                                {{ ($apiData['locked'] ?? 'no') === 'yes' ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Auto Renew:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ ($apiData['auto_renew'] ?? 'no') === 'yes' ? 'success' : 'secondary' }}">
                                                {{ ($apiData['auto_renew'] ?? 'no') === 'yes' ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Private:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ ($apiData['private'] ?? 'no') === 'yes' ? 'success' : 'secondary' }}">
                                                {{ ($apiData['private'] ?? 'no') === 'yes' ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        @if(isset($apiData['nameservers']) && is_array($apiData['nameservers']) && count($apiData['nameservers']) > 0)
                            <div class="mt-3">
                                <h6>Live Nameservers</h6>
                                <ul class="list-unstyled">
                                    @foreach($apiData['nameservers'] as $nameserver)
                                        <li><code>{{ is_string($nameserver) ? $nameserver : 'Invalid nameserver' }}</code></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    @if(!$domain->isExpired())
                        <button type="button" class="btn btn-success btn-block mb-2" data-toggle="modal" data-target="#renewModal">
                            <i class="fas fa-redo"></i> Renew Domain
                        </button>
                    @endif
                    
                    <button type="button" class="btn btn-info btn-block mb-2" data-toggle="modal" data-target="#nameserversModal">
                        <i class="fas fa-server"></i> Update Nameservers
                    </button>
                    
                    <a href="{{ route('domains.edit', $domain) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i> Edit Domain
                    </a>
                    
                    <form action="{{ route('domains.destroy', $domain) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" 
                                onclick="return confirm('Are you sure you want to delete this domain?')">
                            <i class="fas fa-trash"></i> Delete Domain
                        </button>
                    </form>
                </div>
            </div>

            <!-- Domain Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Domain Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $domain->days_until_expiry }}</h3>
                                    <p>Days Left</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $domain->registration_date->diffInYears($domain->expiry_date) }}</h3>
                                    <p>Years</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cost Information</h3>
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
                            <td><strong>Total Cost:</strong></td>
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

    <!-- Update Nameservers Modal -->
    <div class="modal fade" id="nameserversModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('domains.update-nameservers', $domain) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Update Nameservers</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nameserver1">Nameserver 1</label>
                            <input type="text" class="form-control" id="nameserver1" name="nameservers[]" 
                                   value="{{ $domain->getNameserver(0) }}" placeholder="ns1.example.com">
                        </div>
                        <div class="form-group">
                            <label for="nameserver2">Nameserver 2</label>
                            <input type="text" class="form-control" id="nameserver2" name="nameservers[]" 
                                   value="{{ $domain->getNameserver(1) }}" placeholder="ns2.example.com">
                        </div>
                        <div class="form-group">
                            <label for="nameserver3">Nameserver 3 (Optional)</label>
                            <input type="text" class="form-control" id="nameserver3" name="nameservers[]" 
                                   value="{{ $domain->getNameserver(2) }}" placeholder="ns3.example.com">
                        </div>
                        <div class="form-group">
                            <label for="nameserver4">Nameserver 4 (Optional)</label>
                            <input type="text" class="form-control" id="nameserver4" name="nameservers[]" 
                                   value="{{ $domain->getNameserver(3) }}" placeholder="ns4.example.com">
                        </div>
                        <p class="text-muted">
                            This will update the nameservers for {{ $domain->domain_name }} through NameSilo API.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">Update Nameservers</button>
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
