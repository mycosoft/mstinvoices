@extends('adminlte::page')

@section('title', 'Item Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $item->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item active">{{ $item->name }}</li>
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
            <!-- Main Item Information -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box"></i> Item Information
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('Are you sure you want to delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="35%">Name:</th>
                                        <td><strong>{{ $item->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Category:</th>
                                        <td>
                                            @if($item->category)
                                                <span class="badge badge-info">{{ $item->category }}</span>
                                            @else
                                                <span class="text-muted">No category</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>
                                            @if($item->is_service)
                                                <span class="badge badge-warning">Service</span>
                                            @else
                                                <span class="badge badge-success">Product</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($item->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @elseif($item->status == 'inactive')
                                                <span class="badge badge-secondary">Inactive</span>
                                            @else
                                                <span class="badge badge-danger">Discontinued</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if($item->image_url)
                                    <div class="text-center">
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" 
                                             class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                @else
                                    <div class="text-center text-muted p-4 border rounded">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>No image available</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($item->description)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Description:</h6>
                                    <p class="text-muted">{{ $item->description }}</p>
                                </div>
                            </div>
                        @endif

                        @if($item->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Notes:</h6>
                                    <p class="text-muted">{{ $item->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pricing and Stats Sidebar -->
            <div class="col-md-4">
                <!-- Pricing Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-dollar-sign"></i> Pricing
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>Unit Price:</th>
                                <td><strong>${{ number_format($item->unit_price, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Unit Type:</th>
                                <td>{{ ucfirst($item->unit_type) }}</td>
                            </tr>
                            @if($item->cost_price)
                                <tr>
                                    <th>Cost Price:</th>
                                    <td>${{ number_format($item->cost_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Profit Margin:</th>
                                    <td>
                                        @if($item->profit_margin)
                                            <span class="badge badge-{{ $item->profit_margin > 0 ? 'success' : 'danger' }}">
                                                {{ $item->profit_margin }}%
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Tax Information -->
                @if($item->is_taxable)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-receipt"></i> Tax Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Taxable:</th>
                                    <td><span class="badge badge-info">Yes</span></td>
                                </tr>
                                @if($item->tax_rate)
                                    <tr>
                                        <th>Tax Rate:</th>
                                        <td>{{ $item->tax_rate }}%</td>
                                    </tr>
                                    <tr>
                                        <th>Price with Tax:</th>
                                        <td><strong>${{ number_format($item->price_with_tax, 2) }}</strong></td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i> Quick Stats
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>Created:</th>
                                <td>{{ $item->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $item->updated_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Times Used:</th>
                                <td>
                                    <span class="badge badge-info">0</span>
                                    <small class="text-muted d-block">in invoices</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="text-center">
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Item
                    </a>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Back to Items
                    </a>
                    <a href="{{ route('items.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Create New Item
                    </a>
                </div>
            </div>
        </div>

        <!-- Future Invoice Usage (placeholder for when invoice module is created) -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice-dollar"></i> Invoice Usage
                        </h3>
                    </div>
                    <div class="card-body text-center text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>No invoice usage yet</h5>
                        <p>This item hasn't been used in any invoices yet. Once you start creating invoices with this item, the usage history will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th {
            border: none;
            padding: 0.5rem 0.75rem;
            font-weight: 600;
        }
        .table td {
            border: none;
            padding: 0.5rem 0.75rem;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .badge {
            font-size: 0.9em;
        }
        .btn-group .btn {
            margin-right: 5px;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-hide success alerts after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);

        // Handle image loading errors
        $('img').on('error', function() {
            $(this).closest('.text-center').html(
                '<div class="text-center text-muted p-4 border rounded">' +
                '<i class="fas fa-exclamation-triangle fa-3x mb-2"></i>' +
                '<p>Image could not be loaded</p>' +
                '</div>'
            );
        });
    </script>
@stop