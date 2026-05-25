@extends('adminlte::page')

@section('title', 'Expenses')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Expense Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
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
                        <p>Total Expenses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['paid_count'] }}</h3>
                        <p>Paid Expenses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['pending_approval_count'] }}</h3>
                        <p>Pending Approval</p>
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
                        <p>Overdue Expenses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>{{ $stats['billable_count'] }}</h3>
                        <p>Billable Expenses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3>{{ $stats['reimbursable_count'] }}</h3>
                        <p>Reimbursable</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-undo"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $stats['this_month_count'] }}</h3>
                        <p>This Month</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3>{{ $stats['this_year_count'] }}</h3>
                        <p>This Year</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar"></i>
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
                            <div class="col-md-3 text-center">
                                <h4 class="text-primary">{{ $settings->formatCurrency($stats['total_amount']) }}</h4>
                                <p class="text-muted">Total Expenses</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="text-success">{{ $settings->formatCurrency($stats['paid_amount']) }}</h4>
                                <p class="text-muted">Total Paid</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="text-warning">{{ $settings->formatCurrency($stats['unpaid_amount']) }}</h4>
                                <p class="text-muted">Unpaid</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="text-info">{{ $settings->formatCurrency($stats['this_month_amount']) }}</h4>
                                <p class="text-muted">This Month</p>
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
                        <h3 class="card-title">Expenses List</h3>
                        <div class="card-tools">
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Create New Expense
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('expenses.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search expenses..." 
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
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
                                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="date" name="date_from" class="form-control" 
                                                       placeholder="From Date" value="{{ request('date_from') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="date" name="date_to" class="form-control" 
                                                       placeholder="To Date" value="{{ request('date_to') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox d-inline-block mr-3">
                                            <input type="checkbox" name="overdue" value="1" class="custom-control-input" 
                                                   id="overdue" {{ request('overdue') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="overdue">Overdue Only</label>
                                        </div>
                                        <div class="custom-control custom-checkbox d-inline-block mr-3">
                                            <input type="checkbox" name="pending_approval" value="1" class="custom-control-input" 
                                                   id="pending_approval" {{ request('pending_approval') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="pending_approval">Pending Approval</label>
                                        </div>
                                        <div class="custom-control custom-checkbox d-inline-block mr-3">
                                            <input type="checkbox" name="billable" value="1" class="custom-control-input" 
                                                   id="billable" {{ request('billable') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="billable">Billable</label>
                                        </div>
                                        <div class="custom-control custom-checkbox d-inline-block">
                                            <input type="checkbox" name="reimbursable" value="1" class="custom-control-input" 
                                                   id="reimbursable" {{ request('reimbursable') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="reimbursable">Reimbursable</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Expenses Table -->
                        @if($expenses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Expense #</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Project</th>
                                            <th>Vendor</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Payment Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses as $expense)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none">
                                                        {{ $expense->expense_number }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $expense->title }}
                                                    @if($expense->is_billable)
                                                        <span class="badge badge-info badge-sm ml-1">Billable</span>
                                                    @endif
                                                    @if($expense->is_reimbursable)
                                                        <span class="badge badge-warning badge-sm ml-1">Reimbursable</span>
                                                    @endif
                                                </td>
                                                <td>{{ $expense->category }}</td>
                                                <td>
                                                    @if($expense->project)
                                                        <a href="{{ route('projects.show', $expense->project) }}" class="text-primary">
                                                            <i class="fas fa-project-diagram"></i> {{ $expense->project->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $expense->vendor_name ?? '-' }}</td>
                                                <td>{{ $expense->formatted_amount }}</td>
                                                <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                                <td>
                                                    @switch($expense->status)
                                                        @case('draft')
                                                            <span class="badge badge-secondary">Draft</span>
                                                            @break
                                                        @case('pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                            @break
                                                        @case('approved')
                                                            <span class="badge badge-success">Approved</span>
                                                            @break
                                                        @case('rejected')
                                                            <span class="badge badge-danger">Rejected</span>
                                                            @break
                                                        @case('paid')
                                                            <span class="badge badge-success">Paid</span>
                                                            @break
                                                        @case('cancelled')
                                                            <span class="badge badge-dark">Cancelled</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @switch($expense->payment_status)
                                                        @case('unpaid')
                                                            <span class="badge badge-danger">Unpaid</span>
                                                            @break
                                                        @case('partial')
                                                            <span class="badge badge-warning">Partial</span>
                                                            @break
                                                        @case('paid')
                                                            <span class="badge badge-success">Paid</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('expenses.show', $expense) }}" 
                                                           class="btn btn-info btn-sm" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('expenses.edit', $expense) }}" 
                                                           class="btn btn-primary btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('expenses.duplicate', $expense) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-secondary btn-sm" title="Duplicate">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('expenses.destroy', $expense) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
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
                            <div class="d-flex justify-content-center">
                                {{ $expenses->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-receipt fa-5x text-muted mb-3"></i>
                                <h4 class="text-muted">No expenses found</h4>
                                <p class="text-muted">Start by creating your first expense.</p>
                                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create New Expense
                                </a>
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
        .small-box {
            border-radius: 0.25rem;
        }
        .badge-sm {
            font-size: 0.75em;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-submit form when filter changes
            $('select[name="status"], select[name="payment_status"], select[name="category"]').change(function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@stop