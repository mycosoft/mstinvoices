@extends('adminlte::page')

@section('title', 'Items')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Services & Items Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Items</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Services & Items List</h3>
                        <div class="card-tools">
                            <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Service/Item
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('items.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search services & items..." 
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="category" class="form-control">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="type" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>Services</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-sm">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="{{ route('items.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Unit Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->name }}</strong>
                                                @if($item->description)
                                                    <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->category)
                                                    <span class="badge badge-info">{{ $item->category }}</span>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">Service</span>
                                            </td>
                                            <td>
                                                <strong>{{ $item->formatted_unit_price }}</strong>
                                                <br><small class="text-muted">per {{ $item->unit_type }}</small>
                                            </td>
                                            <td>
                                                @if($item->status == 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @elseif($item->status == 'inactive')
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @else
                                                    <span class="badge badge-danger">Discontinued</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('items.show', $item) }}" class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('items.destroy', $item) }}" method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('Are you sure you want to delete this item?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                <div class="py-4">
                                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                                    <h5>No services or items found</h5>
                                                    <p>Get started by <a href="{{ route('items.create') }}">creating your first service or item</a>.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">
                                Showing {{ $items->firstItem() ?: 0 }} to {{ $items->lastItem() ?: 0 }} of {{ $items->total() }} results
                            </div>
                            {{ $items->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn-group .btn {
            margin-right: 2px;
        }
        .table td {
            vertical-align: middle;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-hide success alerts after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
    </script>
@stop