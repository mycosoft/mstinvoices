@extends('adminlte::page')

@section('title', 'Expense Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Expense Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                    <li class="breadcrumb-item active">{{ $expense->expense_number }}</li>
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

        <div class="row">
            <!-- Main Expense Details -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Expense Information</h3>
                        <div class="card-tools">
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('expenses.duplicate', $expense) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-copy"></i> Duplicate
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Expense Number:</strong></td>
                                        <td>{{ $expense->expense_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Title:</strong></td>
                                        <td>{{ $expense->title }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category:</strong></td>
                                        <td>{{ $expense->category }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Expense Date:</strong></td>
                                        <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    </tr>
                                    @if($expense->due_date)
                                    <tr>
                                        <td><strong>Due Date:</strong></td>
                                        <td>{{ $expense->due_date->format('Y-m-d') }}</td>
                                    </tr>
                                    @endif
                                    @if($expense->reference_number)
                                    <tr>
                                        <td><strong>Reference Number:</strong></td>
                                        <td>{{ $expense->reference_number }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @switch($expense->status)
                                                @case('draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge badge-warning">Pending Approval</span>
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
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Status:</strong></td>
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
                                    </tr>
                                    <tr>
                                        <td><strong>Currency:</strong></td>
                                        <td>{{ $expense->currency }}</td>
                                    </tr>
                                    @if($expense->client)
                                    <tr>
                                        <td><strong>Associated Client:</strong></td>
                                        <td>{{ $expense->client->display_name }}</td>
                                    </tr>
                                    @endif
                                    @if($expense->payment_method)
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($expense->description)
                        <div class="row">
                            <div class="col-12">
                                <h5>Description</h5>
                                <p>{{ $expense->description }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Vendor Information -->
                        @if($expense->vendor_name || $expense->vendor_email || $expense->vendor_phone)
                        <div class="row">
                            <div class="col-12">
                                <h5>Vendor Information</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Name:</strong> {{ $expense->vendor_name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Email:</strong> {{ $expense->vendor_email ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Phone:</strong> {{ $expense->vendor_phone ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Additional Fields -->
                        @if($expense->project || $expense->project_code || $expense->department || $expense->location)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Additional Information</h5>
                                <div class="row">
                                    @if($expense->project)
                                    <div class="col-md-4">
                                        <strong>Project:</strong> 
                                        <a href="{{ route('projects.show', $expense->project) }}" class="text-primary">
                                            <i class="fas fa-project-diagram"></i> {{ $expense->project->name }}
                                        </a>
                                    </div>
                                    @elseif($expense->project_code)
                                    <div class="col-md-4">
                                        <strong>Project Code:</strong> {{ $expense->project_code }}
                                    </div>
                                    @endif
                                    @if($expense->department)
                                    <div class="col-md-4">
                                        <strong>Department:</strong> {{ $expense->department }}
                                    </div>
                                    @endif
                                    @if($expense->location)
                                    <div class="col-md-4">
                                        <strong>Location:</strong> {{ $expense->location }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Flags -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex flex-wrap">
                                    @if($expense->is_billable)
                                        <span class="badge badge-info mr-2 mb-2">Billable to Client</span>
                                    @endif
                                    @if($expense->is_reimbursable)
                                        <span class="badge badge-warning mr-2 mb-2">Reimbursable</span>
                                    @endif
                                    @if($expense->is_recurring)
                                        <span class="badge badge-purple mr-2 mb-2">Recurring ({{ ucfirst($expense->recurring_frequency) }})</span>
                                    @endif
                                    @if($expense->requires_approval)
                                        <span class="badge badge-secondary mr-2 mb-2">Requires Approval</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($expense->notes)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Notes</h5>
                                <p>{{ $expense->notes }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Attachments -->
                        @if($expense->attachments && count($expense->attachments) > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Attachments</h5>
                                <div class="row">
                                    @foreach($expense->attachments as $index => $attachment)
                                        <div class="col-md-6 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="fas fa-file mr-1"></i>
                                                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank">
                                                                {{ $attachment['name'] }}
                                                            </a>
                                                            <small class="text-muted d-block">
                                                                {{ number_format($attachment['size'] / 1024, 1) }} KB
                                                            </small>
                                                        </div>
                                                        <form action="{{ route('expenses.delete-attachment', $expense) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="attachment_index" value="{{ $index }}">
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('Are you sure you want to delete this attachment?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Approval Section -->
                @if($expense->requires_approval || $expense->approved_by)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Approval Information</h3>
                    </div>
                    <div class="card-body">
                        @if($expense->approved_by)
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Approved/Rejected By:</strong> {{ $expense->approver->name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Date:</strong> {{ $expense->approved_at->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            @if($expense->approval_notes)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <strong>Notes:</strong> {{ $expense->approval_notes }}
                                </div>
                            </div>
                            @endif
                        @elseif($expense->requires_approval && $expense->status == 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-clock mr-2"></i>
                                This expense is pending approval.
                            </div>
                            
                            <!-- Approval Actions -->
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('expenses.approve', $expense) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="approval_notes">Approval Notes (Optional)</label>
                                            <textarea class="form-control" id="approval_notes" name="approval_notes" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="rejection_notes">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="rejection_notes" name="approval_notes" rows="2" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Financial Summary -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Financial Summary</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Net Amount:</strong></td>
                                <td class="text-right">{{ $expense->formatted_net_amount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tax ({{ $expense->tax_rate }}%):</strong></td>
                                <td class="text-right">{{ $expense->formatted_tax_amount }}</td>
                            </tr>
                            <tr class="bg-light">
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-right"><strong>{{ $expense->formatted_amount }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical btn-block">
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Expense
                            </a>
                            
                            @if($expense->payment_status !== 'paid')
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#markPaidModal">
                                <i class="fas fa-check"></i> Mark as Paid
                            </button>
                            @endif
                            
                            <form action="{{ route('expenses.duplicate', $expense) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm btn-block">
                                    <i class="fas fa-copy"></i> Duplicate
                                </button>
                            </form>
                            
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-block">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Info</h3>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>Created:</strong> {{ $expense->created_at->format('Y-m-d H:i') }}<br>
                            <strong>Updated:</strong> {{ $expense->updated_at->format('Y-m-d H:i') }}<br>
                            @if($expense->is_overdue)
                                <span class="text-danger"><strong>Status:</strong> Overdue by {{ abs($expense->days_until_due) }} days</span>
                            @elseif($expense->due_date && $expense->days_until_due > 0)
                                <span class="text-info"><strong>Due in:</strong> {{ $expense->days_until_due }} days</span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    <div class="modal fade" id="markPaidModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('expenses.mark-paid', $expense) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Mark Expense as Paid</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                                <option value="paypal">PayPal</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark as Paid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .badge-purple {
            background-color: #6f42c1;
            color: white;
        }
    </style>
@stop