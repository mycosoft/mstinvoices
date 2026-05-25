@extends('adminlte::page')

@section('title', 'Project Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Project Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Project Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Project Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalProjects }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $activeProjects }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <a href="{{ route('projects.index', ['status' => 'in_progress']) }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $overdueTasks }}</h3>
                    <p>Overdue Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalBudget }}</h3>
                    <p>Total Budget</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Project Health Overview -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Project Health Overview</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($projects as $project)
                            <div class="col-md-4 mb-3">
                                <div class="card border-{{ $project->health_score >= 80 ? 'success' : ($project->health_score >= 60 ? 'warning' : 'danger') }}">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Progress</small>
                                                <div class="progress mb-2" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $project->progress_percentage }}%" 
                                                         aria-valuenow="{{ $project->progress_percentage }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $project->progress_percentage }}%
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Health Score</small>
                                                <div class="progress mb-2" style="height: 20px;">
                                                    <div class="progress-bar 
                                                        @if($project->health_score >= 80) bg-success
                                                        @elseif($project->health_score >= 60) bg-warning
                                                        @else bg-danger
                                                        @endif" 
                                                         role="progressbar" 
                                                         style="width: {{ $project->health_score }}%" 
                                                         aria-valuenow="{{ $project->health_score }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $project->health_score }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Tasks</small>
                                                <div>{{ $project->tasks->count() }} total</div>
                                                <div class="text-success">{{ $project->tasks->where('status', 'completed')->count() }} completed</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Budget</small>
                                                <div>{{ $settings->formatCurrency($project->budget ?? 0) }}</div>
                                                <div class="text-{{ $project->budget_remaining >= 0 ? 'success' : 'danger' }}">
                                                    {{ $settings->formatCurrency($project->budget_remaining) }} remaining
                                                </div>
                                            </div>
                                        </div>
                                        @if($project->overdue_tasks_count > 0)
                                            <div class="alert alert-danger alert-sm mt-2 mb-0">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $project->overdue_tasks_count }} overdue tasks
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Tasks</h3>
                </div>
                <div class="card-body">
                    @if($recentTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTasks as $task)
                                        <tr>
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->assignee)
                                                    <br><small class="text-muted">Assigned to: {{ $task->assignee->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ 
                                                    $task->status === 'completed' ? 'success' : 
                                                    ($task->status === 'in_progress' ? 'warning' : 
                                                    ($task->status === 'pending' ? 'info' : 
                                                    ($task->status === 'on_hold' ? 'secondary' : 
                                                    ($task->status === 'cancelled' ? 'danger' : 'secondary')))) 
                                                }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 15px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $task->percent_complete }}%" 
                                                         aria-valuenow="{{ $task->percent_complete }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $task->percent_complete }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent tasks found.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upcoming Deadlines</h3>
                </div>
                <div class="card-body">
                    @if($upcomingDeadlines->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Due Date</th>
                                        <th>Priority</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingDeadlines as $task)
                                        <tr class="{{ $task->due_date < now() ? 'table-danger' : ($task->due_date < now()->addDays(3) ? 'table-warning' : '') }}">
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->assignee)
                                                    <br><small class="text-muted">Assigned to: {{ $task->assignee->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a>
                                            </td>
                                            <td>
                                                {{ $task->due_date->format('M d, Y') }}
                                                @if($task->due_date < now())
                                                    <br><small class="text-danger">Overdue</small>
                                                @elseif($task->due_date < now()->addDays(3))
                                                    <br><small class="text-warning">Due soon</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No upcoming deadlines found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Project
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('projects.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-list"></i> View All Projects
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('invoices.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-file-invoice"></i> Create Invoice
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('expenses.create') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-receipt"></i> Add Expense
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
