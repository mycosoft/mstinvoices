@extends('adminlte::page')

@section('title', 'Quotations')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quotation Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Quotations</li>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Total Quotations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['accepted'] }}</h3>
                        <p>Accepted Quotations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['sent'] }}</h3>
                        <p>Sent Quotations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['expired'] }}</h3>
                        <p>Expired Quotations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quotation Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h4 class="text-primary">{{ $settings->formatCurrency($stats['total_value']) }}</h4>
                                <p class="text-muted">Total Quoted</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4 class="text-success">{{ $settings->formatCurrency($stats['accepted_value']) }}</h4>
                                <p class="text-muted">Accepted Value</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4 class="text-info">{{ $stats['acceptance_rate'] }}%</h4>
                                <p class="text-muted">Acceptance Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quotations List</h3>
                        <div class="card-tools">
                            <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Create New Quotation
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('quotations.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search quotations..." 
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                            <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="client_id" class="form-control">
                                            <option value="">All Clients</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" name="date_from" class="form-control" 
                                               placeholder="From Date" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" name="date_to" class="form-control" 
                                               placeholder="To Date" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-sm">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="custom-control custom-checkbox d-inline-block mr-3">
                                        <input type="checkbox" name="expired" value="1" class="custom-control-input" 
                                               id="expired" {{ request('expired') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="expired">Expired Only</label>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if($quotations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Quotation #</th>
                                            <th>Client</th>
                                            <th>Quotation Date</th>
                                            <th>Valid Until</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotations as $quotation)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-primary">
                                                        {{ $quotation->quotation_number }}
                                                    </a>
                                                    @if($quotation->reference_number)
                                                        <br><small class="text-muted">Ref: {{ $quotation->reference_number }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $quotation->client->display_name }}</strong>
                                                    @if($quotation->client->company_name && $quotation->client->company_name !== $quotation->client->name)
                                                        <br><small class="text-muted">{{ $quotation->client->company_name }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $quotation->quotation_date->format('M d, Y') }}</td>
                                                <td>
                                                    {{ $quotation->valid_until->format('M d, Y') }}
                                                    @if($quotation->is_expired)
                                                        <br><small class="text-danger">Expired</small>
                                                    @elseif($quotation->days_until_expiry <= 3)
                                                        <br><small class="text-warning">Expires soon</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClasses = [
                                                            'draft' => 'secondary',
                                                            'sent' => 'info',
                                                            'viewed' => 'primary',
                                                            'accepted' => 'success',
                                                            'rejected' => 'danger',
                                                            'expired' => 'warning',
                                                            'converted' => 'dark'
                                                        ];
                                                        $statusClass = $statusClasses[$quotation->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge badge-{{ $statusClass }}">
                                                        {{ ucfirst($quotation->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $settings->formatCurrency($quotation->total_amount) }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('quotations.show', $quotation) }}" 
                                                           class="btn btn-info btn-sm" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(!in_array($quotation->status, ['accepted', 'converted']))
                                                            <a href="{{ route('quotations.edit', $quotation) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('quotations.pdf', $quotation) }}" 
                                                           class="btn btn-secondary btn-sm" title="Download PDF" target="_blank">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                        @if(!in_array($quotation->status, ['accepted', 'converted', 'rejected']))
                                                            <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" 
                                                                  style="display: inline-block;" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $quotations->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No quotations found</h5>
                                <p class="text-muted">
                                    @if(request()->hasAny(['search', 'status', 'client_id', 'date_from', 'date_to', 'expired']))
                                        Try adjusting your search criteria or 
                                        <a href="{{ route('quotations.index') }}">view all quotations</a>.
                                    @else
                                        Get started by creating your first quotation.
                                    @endif
                                </p>
                                @if(!request()->hasAny(['search', 'status', 'client_id', 'date_from', 'date_to', 'expired']))
                                    <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create New Quotation
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .inner h3 {
            font-size: 2.2rem;
        }
        .table th {
            border-top: none;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .btn-group .btn:last-child {
            margin-right: 0;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-submit form when checkbox changes
            $('input[name="expired"]').change(function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@stop