@extends('adminlte::page')

@section('title', 'Project Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $project->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item active">{{ $project->name }}</li>
                </ol>
                    </div>
    </div>
</div>

@stop

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Are you sure you want to delete this project?')" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Project Information Row -->
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Project Details</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Client:</strong> 
                        @if($project->client)
                            <a href="{{ route('clients.show', $project->client) }}">{{ $project->client->name }}</a>
                        @else
                            <span class="text-muted">No client assigned</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Status:</strong> 
                        <span class="badge badge-{{ 
                            $project->status === 'completed' ? 'success' : 
                            ($project->status === 'in_progress' ? 'warning' : 
                            ($project->status === 'pending' ? 'info' : 
                            ($project->status === 'cancelled' ? 'danger' : 'secondary'))) 
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </p>
                    <p class="mb-1"><strong>Start Date:</strong> 
                        {{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}
                    </p>
                    <p class="mb-1"><strong>End Date:</strong> 
                        {{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}
                    </p>
                    <p class="mb-1"><strong>Budget:</strong> 
                        {{ $project->budget ? $settings->formatCurrency($project->budget) : 'Not set' }}
                    </p>
                    <p class="mb-0"><strong>Progress:</strong> 
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $project->progress_percentage }}%" 
                                 aria-valuenow="{{ $project->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $project->progress_percentage }}%
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Financial Summary</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Budget:</strong> 
                        {{ $project->budget ? $settings->formatCurrency($project->budget) : 'Not set' }}
                    </p>
                    <p class="mb-1"><strong>Total Invoiced:</strong> 
                        {{ $settings->formatCurrency($project->total_invoiced) }}
                    </p>
                    <p class="mb-1"><strong>Total Expenses:</strong> 
                        {{ $settings->formatCurrency($project->total_expenses) }}
                    </p>
                    <p class="mb-0"><strong>Budget Remaining:</strong> 
                        <span class="text-{{ $project->budget_remaining >= 0 ? 'success' : 'danger' }}">
                            {{ $settings->formatCurrency($project->budget_remaining) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Project Statistics</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Total Tasks:</strong> {{ $project->tasks->count() }}</p>
                    <p class="mb-1"><strong>Completed Tasks:</strong> {{ $project->tasks->where('status', 'completed')->count() }}</p>
                    <p class="mb-1"><strong>Overdue Tasks:</strong> 
                        <span class="text-{{ $project->overdue_tasks_count > 0 ? 'danger' : 'success' }}">
                            {{ $project->overdue_tasks_count }}
                        </span>
                    </p>
                    <p class="mb-1"><strong>Tasks Due Today:</strong> {{ $project->tasks_due_today_count }}</p>
                    <p class="mb-1"><strong>Estimated Hours:</strong> {{ $project->total_estimated_hours }}h</p>
                    <p class="mb-0"><strong>Efficiency:</strong> 
                        <span class="text-{{ $project->efficiency <= 100 ? 'success' : 'warning' }}">
                            {{ $project->efficiency }}%
                        </span>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Description Row -->
    @if($project->description)
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Description</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $project->description }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tasks Section - Full Width -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Tasks</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.tasks.store', $project) }}" class="row g-2 align-items-end mb-3">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input name="title" class="form-control" required placeholder="Task title">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Assignee</label>
                            <select name="assigned_to" class="form-control">
                                <option value="">Unassigned</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-control">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Est. Hours</label>
                            <input type="number" name="estimated_hours" step="0.5" min="0" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Progress</label>
                            <input type="number" name="percent_complete" min="0" max="100" class="form-control" value="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <input type="hidden" name="status" value="pending">
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Assignee</th>
                                    <th>Priority</th>
                                    <th>Est. Hours</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->tasks as $task)
                                    <tr data-task-id="{{ $task->id }}">
                                        <td>
                                            <input type="text" class="form-control form-control-sm task-title" 
                                                   value="{{ $task->title }}" 
                                                   data-task-id="{{ $task->id }}" 
                                                   data-field="title">
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm task-assigned-to" 
                                                    data-task-id="{{ $task->id }}" 
                                                    data-field="assigned_to">
                                                <option value="">Unassigned</option>
                                                @foreach(\App\Models\User::all() as $user)
                                                    <option value="{{ $user->id }}" @selected($task->assigned_to == $user->id)>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm task-priority" 
                                                    data-task-id="{{ $task->id }}" 
                                                    data-field="priority">
                                                @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                                                    <option value="{{ $priority }}" @selected($task->priority == $priority)>
                                                        {{ ucfirst($priority) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm task-estimated-hours" 
                                                   step="0.5" min="0" 
                                                   value="{{ $task->estimated_hours }}" 
                                                   data-task-id="{{ $task->id }}" 
                                                   data-field="estimated_hours">
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control task-progress" 
                                                       min="0" max="100" 
                                                       value="{{ $task->percent_complete }}" 
                                                       data-task-id="{{ $task->id }}" 
                                                       data-field="percent_complete">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm task-status" 
                                                    data-task-id="{{ $task->id }}" 
                                                    data-field="status">
                                                @foreach(['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'] as $status)
                                                    <option value="{{ $status }}" @selected($task->status == $status)>
                                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm task-due-date" 
                                                   value="{{ optional($task->due_date)->format('Y-m-d') }}" 
                                                   data-task-id="{{ $task->id }}" 
                                                   data-field="due_date">
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-success btn-sm update-task" 
                                                    data-task-id="{{ $task->id }}" 
                                                    title="Update Task">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-task" 
                                                    data-task-id="{{ $task->id }}" 
                                                    title="Delete Task">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No tasks yet. Add your first task above.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Linked Invoices</h3>
                </div>
                <div class="card-body">
                    @if($project->invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->invoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a>
                                            </td>
                                            <td>{{ $invoice->client->name }}</td>
                                            <td>{{ $settings->formatCurrency($invoice->total_amount) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'sent' ? 'info' : 'secondary') }}">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No invoices linked to this project yet.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Linked Expenses</h3>
                </div>
                <div class="card-body">
                    @if($project->expenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Expense #</th>
                                        <th>Title</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->expenses as $expense)
                                        <tr>
                                            <td>
                                                <a href="{{ route('expenses.show', $expense) }}">{{ $expense->expense_number }}</a>
                                            </td>
                                            <td>{{ $expense->title }}</td>
                                            <td>{{ $settings->formatCurrency($expense->amount) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($expense->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No expenses linked to this project yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    console.log('Task management JavaScript loaded');
    
    // Update task on field change
    $(document).on('click', '.update-task', function(e) {
        e.preventDefault();
        console.log('Update task clicked');
        
        const taskId = $(this).data('task-id');
        const row = $(this).closest('tr');
        const data = {
            title: row.find('.task-title').val(),
            assigned_to: row.find('.task-assigned-to').val(),
            priority: row.find('.task-priority').val(),
            estimated_hours: row.find('.task-estimated-hours').val(),
            due_date: row.find('.task-due-date').val(),
            percent_complete: row.find('.task-progress').val(),
            status: row.find('.task-status').val(),
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        console.log('Updating task:', taskId, data);

        $.ajax({
            url: '{{ route("projects.tasks.update", ["project" => $project->id, "task" => ":taskId"]) }}'.replace(':taskId', taskId),
            method: 'POST',
            data: data,
            success: function(response) {
                console.log('Task updated successfully:', response);
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Task updated successfully!');
                } else {
                    alert('Task updated successfully!');
                }
                
                // Update project progress if needed
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error updating task:', xhr.responseText);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error updating task: ' + error);
                } else {
                    alert('Error updating task: ' + error);
                }
            }
        });
    });

    // Delete task
    $(document).on('click', '.delete-task', function(e) {
        e.preventDefault();
        console.log('Delete task clicked');
        
        if (confirm('Are you sure you want to delete this task?')) {
            const taskId = $(this).data('task-id');
            const row = $(this).closest('tr');

            console.log('Deleting task:', taskId);

            $.ajax({
                url: '{{ route("projects.tasks.destroy", ["project" => $project->id, "task" => ":taskId"]) }}'.replace(':taskId', taskId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    console.log('Task deleted successfully:', response);
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Task deleted successfully!');
                    } else {
                        alert('Task deleted successfully!');
                    }
                    row.remove();
                    
                    // Reload to update project progress
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting task:', xhr.responseText);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Error deleting task: ' + error);
                    } else {
                        alert('Error deleting task: ' + error);
                    }
                }
            });
        }
    });

    // Auto-update progress when status changes to completed
    $(document).on('change', '.task-status', function() {
        const status = $(this).val();
        const progressInput = $(this).closest('tr').find('.task-progress');
        
        if (status === 'completed') {
            progressInput.val(100);
        } else if (status === 'cancelled') {
            progressInput.val(0);
        }
    });
});
</script>
@stop
