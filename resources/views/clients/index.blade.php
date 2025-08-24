@extends('adminlte::page')

@section('title', 'Clients')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Clients Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Clients</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Clients</h3>
                    <div class="card-tools">
                        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Client
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('clients.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search clients..." value="{{ request('search') }}">
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
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">Filter</button>
                                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    @if($clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name/Company</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th width="200px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                        <tr>
                                            <td>
                                                <strong>{{ $client->display_name }}</strong>
                                                @if($client->company_name && $client->name !== $client->company_name)
                                                    <br><small class="text-muted">{{ $client->name }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $client->email }}</td>
                                            <td>{{ $client->phone ?: 'N/A' }}</td>
                                            <td>{{ $client->city ? $client->city . ', ' . $client->country : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $client->status === 'active' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($client->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $client->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('clients.show', $client) }}" 
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('clients.edit', $client) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('clients.destroy', $client) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                title="Delete" 
                                                                onclick="return confirm('Are you sure you want to delete this client?')">
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
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info">
                                    Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} 
                                    of {{ $clients->total() }} results
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                {{ $clients->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4>No clients found</h4>
                            <p class="text-muted">
                                @if(request('search'))
                                    No clients match your search criteria.
                                @else
                                    You haven't added any clients yet.
                                @endif
                            </p>
                            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Your First Client
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