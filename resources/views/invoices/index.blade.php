@extends('adminlte::page')

@section('title', 'Invoices')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Invoice Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Invoices</li>
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
                        <h3>{{ $stats['total_count'] }}</h3>
                        <p>Total Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['paid_count'] }}</h3>
                        <p>Paid Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['draft_count'] }}</h3>
                        <p>Draft Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['pending_count'] }}</h3>
                        <p>Pending Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['overdue_count'] }}</h3>
                        <p>Overdue Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Financial Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h4 class="text-primary">{{ $settings->formatCurrency($stats['total_amount']) }}</h4>
                                <p class="text-muted">Total Invoiced</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4 class="text-success">{{ $settings->formatCurrency($stats['paid_amount']) }}</h4>
                                <p class="text-muted">Total Paid</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4 class="text-warning">{{ $settings->formatCurrency($stats['outstanding_amount']) }}</h4>
                                <p class="text-muted">Outstanding</p>
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
                        <h3 class="card-title">Invoices List</h3>
                        <div class="card-tools">
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Create New Invoice
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('invoices.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search invoices..." 
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="payment_status" class="form-control">
                                            <option value="">All Payment Status</option>
                                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
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
                                        <select name="project_id" class="form-control">
                                            <option value="">All Projects</option>
                                            @foreach($projects ?? [] as $project)
                                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-sm">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                        <div class="custom-control custom-checkbox d-inline-block ml-2">
                                            <input type="checkbox" name="overdue" value="1" class="custom-control-input" 
                                                   id="overdue" {{ request('overdue') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="overdue">Overdue Only</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_from" class="form-label">Date From:</label>
                                        <input type="date" name="date_from" class="form-control" 
                                               value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_to" class="form-label">Date To:</label>
                                        <input type="date" name="date_to" class="form-control" 
                                               value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Invoices Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Client</th>
                                        <th>Project</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr class="{{ $invoice->is_overdue ? 'table-danger' : '' }}">
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none">
                                                    <strong>{{ $invoice->invoice_number }}</strong>
                                                </a>
                                                @if($invoice->reference_number)
                                                    <br><small class="text-muted">Ref: {{ $invoice->reference_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $invoice->client->display_name }}</strong>
                                                @if($invoice->client->company_name && $invoice->client->name !== $invoice->client->company_name)
                                                    <br><small class="text-muted">{{ $invoice->client->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($invoice->project)
                                                    <a href="{{ route('projects.show', $invoice->project) }}" class="text-primary">
                                                        <i class="fas fa-project-diagram"></i> {{ $invoice->project->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                            <td>
                                                {{ $invoice->due_date->format('M d, Y') }}
                                                @if($invoice->is_overdue)
                                                    <br><small class="text-danger">{{ abs($invoice->days_until_due) }} days overdue</small>
                                                @elseif($invoice->days_until_due <= 7 && $invoice->days_until_due > 0)
                                                    <br><small class="text-warning">{{ $invoice->days_until_due }} days left</small>
                                                @endif
                                            </td>
                                            <td><strong>{{ $settings->formatCurrency($invoice->total_amount) }}</strong></td>
                                            <td>{{ $settings->formatCurrency($invoice->paid_amount) }}</td>
                                            <td>
                                                <strong class="{{ $invoice->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ $settings->formatCurrency($invoice->balance_due) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($invoice->status == 'draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                @elseif($invoice->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($invoice->status == 'sent')
                                                    <span class="badge badge-info">Sent</span>
                                                @elseif($invoice->status == 'viewed')
                                                    <span class="badge badge-primary">Viewed</span>
                                                @elseif($invoice->status == 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif($invoice->status == 'partial')
                                                    <span class="badge badge-warning">Partial</span>
                                                @elseif($invoice->status == 'overdue')
                                                    <span class="badge badge-danger">Overdue</span>
                                                @else
                                                    <span class="badge badge-dark">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($invoice->payment_status == 'unpaid')
                                                    <span class="badge badge-danger">Unpaid</span>
                                                @elseif($invoice->payment_status == 'partial')
                                                    <span class="badge badge-warning">Partial</span>
                                                    <br><small class="text-muted">{{ $invoice->payment_progress }}%</small>
                                                @elseif($invoice->payment_status == 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <span class="badge badge-info">Refunded</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('invoices.preview', $invoice) }}" class="btn btn-primary btn-sm" title="Preview" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-list"></i>
                                                    </a>
                                                    @if($invoice->payment_status !== 'paid')
                                                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('invoices.duplicate', $invoice) }}" method="POST" 
                                                          style="display: inline-block;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-secondary btn-sm" title="Duplicate">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>
                                                    @if($invoice->status === 'draft')
                                                        <form action="{{ route('invoices.mark-sent', $invoice) }}" method="POST" 
                                                              style="display: inline-block;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary btn-sm" title="Mark as Sent">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($invoice->payment_status !== 'paid')
                                                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" 
                                                              style="display: inline-block;"
                                                              onsubmit="return confirm('Are you sure you want to delete this invoice?')">
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
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                <div class="py-4">
                                                    <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                                    <h5>No invoices found</h5>
                                                    <p>Get started by <a href="{{ route('invoices.create') }}">creating your first invoice</a>.</p>
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
                                Showing {{ $invoices->firstItem() ?: 0 }} to {{ $invoices->lastItem() ?: 0 }} of {{ $invoices->total() }} results
                            </div>
                            {{ $invoices->withQueryString()->links() }}
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
        .table-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }
        .small-box {
            border-radius: 5px;
        }
        .small-box .icon {
            font-size: 70px;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-hide success/error alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@stop