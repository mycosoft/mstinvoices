@extends('adminlte::page')

@section('title', 'Domain Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Domain Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Domains</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Domains</p>
                </div>
                <div class="icon">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['active'] }}</h3>
                    <p>Active Domains</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['expiring_soon'] }}</h3>
                    <p>Expiring Soon</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['expired'] }}</h3>
                    <p>Expired Domains</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Domains</h3>
                    <div class="card-tools">
                        <form action="{{ route('domains.sync-from-account') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm mr-2" 
                                    onclick="return confirm('This will sync domains from your NameSilo account. Continue?')">
                                <i class="fas fa-sync"></i> Sync from NameSilo
                            </button>
                        </form>
                        <a href="{{ route('domains.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Domain
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('domains.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search domains..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="expiry_filter" class="form-control">
                                    <option value="">All Expiry</option>
                                    <option value="expiring_soon" {{ request('expiry_filter') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                                    <option value="expired" {{ request('expiry_filter') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="expires_this_month" {{ request('expiry_filter') == 'expires_this_month' ? 'selected' : '' }}>Expires This Month</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info">Filter</button>
                                <a href="{{ route('domains.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    @if($domains->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Domain Name</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Registrar</th>
                                        <th>Provider/NS</th>
                                        <th>Expiry Date</th>
                                        <th>Days Left</th>
                                        <th>Auto Renew</th>
                                        <th width="200px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($domains as $domain)
                                        <tr class="{{ $domain->isExpired() ? 'table-danger' : ($domain->isExpiringSoon(30) ? 'table-warning' : '') }}">
                                            <td>
                                                <strong>{{ $domain->domain_name }}</strong>
                                                @if($domain->privacy_protection)
                                                    <br><small class="text-muted"><i class="fas fa-shield-alt"></i> Privacy Protected</small>
                                                @endif
                                                @if($domain->isExpired())
                                                    <br><small class="text-danger"><i class="fas fa-exclamation-circle"></i> EXPIRED</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($domain->client)
                                                    <a href="{{ route('clients.show', $domain->client) }}">
                                                        {{ $domain->client->display_name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No client</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $domain->status_badge_class }}">
                                                    {{ ucfirst($domain->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $domain->registrar }}</td>
                                            <td>
                                                @if($domain->provider)
                                                    <span class="text-primary">{{ $domain->provider }}</span>
                                                @endif
                                                @if($domain->nameservers && count($domain->nameservers) > 0)
                                                    <br><small class="text-muted" title="Nameservers">
                                                        <i class="fas fa-server"></i> {{ implode(', ', array_slice($domain->nameservers, 0, 2)) }}
                                                        @if(count($domain->nameservers) > 2)...
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $domain->expiry_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($domain->isExpired())
                                                    <span class="text-danger">
                                                        <i class="fas fa-times"></i> Expired
                                                    </span>
                                                @elseif($domain->isExpiringSoon(30))
                                                    <span class="text-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> {{ $domain->days_until_expiry }} days
                                                    </span>
                                                @else
                                                    <span class="text-success">
                                                        <i class="fas fa-check"></i> {{ $domain->days_until_expiry }} days
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($domain->auto_renew)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('domains.show', $domain) }}" 
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('domains.edit', $domain) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($domain->isExpired())
                                                        <a href="{{ route('domains.show', $domain) }}#renew" 
                                                           class="btn btn-success btn-sm" title="Renew Domain">
                                                            <i class="fas fa-redo"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('domains.destroy', $domain) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                title="Delete" 
                                                                onclick="return confirm('Are you sure you want to delete this domain?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                Showing {{ $domains->firstItem() ?? 0 }} to {{ $domains->lastItem() ?? 0 }}
                                of {{ $domains->total() }} domains
                            </div>
                            <div>
                                @if($domains->hasPages())
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            @if($domains->onFirstPage())
                                                <li class="page-item disabled"><span class="page-link">First</span></li>
                                                <li class="page-item disabled"><span class="page-link">&lsaquo; Previous</span></li>
                                            @else
                                                <li class="page-item"><a class="page-link" href="{{ $domains->url(1) }}">First</a></li>
                                                <li class="page-item"><a class="page-link" href="{{ $domains->previousPageUrl() }}">&lsaquo; Previous</a></li>
                                            @endif

                                            @foreach($domains->getUrlRange(max(1, $domains->currentPage() - 2), min($domains->lastPage(), $domains->currentPage() + 2)) as $page => $url)
                                                @if($page == $domains->currentPage())
                                                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                                @else
                                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                                @endif
                                            @endforeach

                                            @if($domains->hasMorePages())
                                                <li class="page-item"><a class="page-link" href="{{ $domains->nextPageUrl() }}">Next &rsaquo;</a></li>
                                                <li class="page-item"><a class="page-link" href="{{ $domains->url($domains->lastPage()) }}">Last</a></li>
                                            @else
                                                <li class="page-item disabled"><span class="page-link">Next &rsaquo;</span></li>
                                                <li class="page-item disabled"><span class="page-link">Last</span></li>
                                            @endif
                                        </ul>
                                    </nav>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                            <h4>No domains found</h4>
                            <p class="text-muted">
                                @if(request('search') || request('status') || request('expiry_filter'))
                                    No domains match your search criteria.
                                @else
                                    You haven't added any domains yet.
                                @endif
                            </p>
                            <a href="{{ route('domains.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Your First Domain
                            </a>
                        </div>
                    @endif
                </div>
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
