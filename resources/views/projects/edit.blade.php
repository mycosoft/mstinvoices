@extends('adminlte::page')

@section('title', 'Edit Project')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Project: {{ $project->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Project Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   required value="{{ old('name', $project->name) }}" placeholder="Enter project name">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id">Client</label>
                            <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror">
                                <option value="">Select client (optional)</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id', $project->client_id)==$client->id)>{{ $client->name }}</option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                @foreach(['pending', 'in_progress', 'completed', 'cancelled'] as $st)
                                    <option value="{{ $st }}" @selected(old('status', $project->status)===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}">
                            @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}">
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="budget">Budget</label>
                            <input type="number" step="0.01" min="0" name="budget" id="budget" 
                                   class="form-control @error('budget') is-invalid @enderror" 
                                   value="{{ old('budget', $project->budget) }}" placeholder="0.00">
                            @error('budget')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror">
                                <option value="UGX" @selected(old('currency', $project->currency ?? $settings->default_currency) == 'UGX')>UGX - Ugandan Shilling</option>
                                <option value="USD" @selected(old('currency', $project->currency ?? $settings->default_currency) == 'USD')>USD - US Dollar</option>
                                <option value="EUR" @selected(old('currency', $project->currency) == 'EUR')>EUR - Euro</option>
                                <option value="GBP" @selected(old('currency', $project->currency) == 'GBP')>GBP - British Pound</option>
                            </select>
                            @error('currency')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" placeholder="Enter project description">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save"></i> Update Project
                    </button>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View Project
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Task Management Section -->
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Project Tasks</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTaskModal">
                    <i class="fas fa-plus"></i> Add Task
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($project->tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Description</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Est. Hours</th>
                                <th>Due Date</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->tasks as $task)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm task-title" 
                                               value="{{ $task->title }}" 
                                               data-task-id="{{ $task->id }}" 
                                               data-field="title">
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm task-description" 
                                                  rows="2" 
                                                  data-task-id="{{ $task->id }}" 
                                                  data-field="description">{{ $task->description }}</textarea>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm task-assigned-to" 
                                                data-task-id="{{ $task->id }}" 
                                                data-field="assigned_to">
                                            <option value="">Unassigned</option>
                                            @foreach($users ?? [] as $user)
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
                                        <input type="date" class="form-control form-control-sm task-due-date" 
                                               value="{{ optional($task->due_date)->format('Y-m-d') }}" 
                                               data-task-id="{{ $task->id }}" 
                                               data-field="due_date">
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
                                        <button type="button" class="btn btn-success btn-sm update-task" 
                                                data-task-id="{{ $task->id }}" 
                                                title="Update Task">
                                            <i class="fas fa-save"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-task" 
                                                data-task-id="{{ $task->id }}" 
                                                title="Delete Task">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No tasks yet</h5>
                    <p class="text-muted">Create your first task to get started.</p>
                </div>
            @endif
        </div>
    </div>
</div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addTaskForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="task_title">Task Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="task_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="task_description">Description</label>
                        <textarea class="form-control" id="task_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="task_assigned_to">Assign To</label>
                        <select class="form-control" id="task_assigned_to" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="task_priority">Priority</label>
                        <select class="form-control" id="task_priority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="task_estimated_hours">Estimated Hours</label>
                        <input type="number" class="form-control" id="task_estimated_hours" name="estimated_hours" step="0.5" min="0" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label for="task_due_date">Due Date</label>
                        <input type="date" class="form-control" id="task_due_date" name="due_date">
                    </div>
                    <div class="form-group">
                        <label for="task_percent_complete">Progress (%)</label>
                        <input type="number" class="form-control" id="task_percent_complete" name="percent_complete" min="0" max="100" value="0">
                    </div>
                    <div class="form-group">
                        <label for="task_status">Status</label>
                        <select class="form-control" id="task_status" name="status">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Task Management -->
<script>
// Ensure jQuery is loaded before running the script
function initTaskManagement() {
    if (typeof $ === 'undefined') {
        console.log('jQuery not loaded yet, retrying in 100ms...');
        setTimeout(initTaskManagement, 100);
        return;
    }
    
    console.log('Document ready - jQuery loaded successfully');
    console.log('Found update-task buttons:', $('.update-task').length);
    
    // Update task on field change - using event delegation
    $(document).on('click', '.update-task', function() {
        console.log('Update task button clicked!');
        alert('Save button clicked!'); // Temporary debug alert
        const taskId = $(this).data('task-id');
        const row = $(this).closest('tr');
        
        // Disable button to prevent double clicks
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        const assignedTo = row.find('.task-assigned-to').val();
        const dueDate = row.find('.task-due-date').val();
        
        const data = {
            title: row.find('.task-title').val(),
            description: row.find('.task-description').val(),
            assigned_to: assignedTo && assignedTo !== '' ? assignedTo : null,
            due_date: dueDate && dueDate !== '' ? dueDate : null,
            percent_complete: parseInt(row.find('.task-progress').val()) || 0,
            status: row.find('.task-status').val(),
            priority: row.find('.task-priority').val() || 'medium',
            estimated_hours: parseFloat(row.find('.task-estimated-hours').val()) || 0,
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        console.log('Updating task:', taskId, data);

        $.ajax({
            url: '{{ route("projects.tasks.update", ["project" => $project->id, "task" => ":taskId"]) }}'.replace(':taskId', taskId),
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                console.log('Task updated successfully:', response);
                if (typeof toastr !== 'undefined') {
                    toastr.success('Task updated successfully!');
                } else {
                    alert('Task updated successfully!');
                }
                // Re-enable button
                $('.update-task[data-task-id="' + taskId + '"]').prop('disabled', false).html('<i class="fas fa-save"></i>');
                // Update project progress if needed
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error updating task:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                
                let errorMessage = 'Unknown error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error updating task: ' + errorMessage);
                } else {
                    alert('Error updating task: ' + errorMessage);
                }
                
                // Re-enable button on error
                $('.update-task[data-task-id="' + taskId + '"]').prop('disabled', false).html('<i class="fas fa-save"></i>');
            }
        });
    });

    // Delete task - using event delegation
    $(document).on('click', '.delete-task', function() {
        if (confirm('Are you sure you want to delete this task?')) {
            const taskId = $(this).data('task-id');
            const row = $(this).closest('tr');

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
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting task:', xhr.responseText);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Error deleting task: ' + (xhr.responseJSON?.message || error));
                    } else {
                        alert('Error deleting task: ' + (xhr.responseJSON?.message || error));
                    }
                }
            });
        }
    });

    // Add new task
    $('#addTaskForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("projects.tasks.store", $project) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Task created successfully:', response);
                if (typeof toastr !== 'undefined') {
                    toastr.success('Task created successfully!');
                } else {
                    alert('Task created successfully!');
                }
                $('#addTaskModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error creating task:', xhr.responseText);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error creating task: ' + (xhr.responseJSON?.message || error));
                } else {
                    alert('Error creating task: ' + (xhr.responseJSON?.message || error));
                }
            }
        });
    });
}

// Start the initialization
initTaskManagement();
</script>
@stop
