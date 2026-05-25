@extends('adminlte::page')

@section('title', 'Projects')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Projects</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Projects</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Project
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Projects</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search project name...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        @foreach(['pending', 'in_progress', 'completed', 'cancelled'] as $st)
                            <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="client_id" class="form-control">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected(request('client_id')==$client->id)>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-secondary w-100" type="submit">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Projects</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Budget</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}" class="font-weight-bold">
                                        {{ $project->name }}
                                    </a>
                                    @if($project->description)
                                        <br><small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($project->client)
                                        <a href="{{ route('clients.show', $project->client) }}">{{ $project->client->name }}</a>
                                    @else
                                        <span class="text-muted">No client</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $project->status === 'completed' ? 'success' : 
                                        ($project->status === 'in_progress' ? 'warning' : 
                                        ($project->status === 'pending' ? 'info' : 
                                        ($project->status === 'cancelled' ? 'danger' : 'secondary'))) 
                                    }}">
                                        {{ ucfirst(str_replace('_',' ', $project->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($project->budget)
                                        {{ $settings->formatCurrency($project->budget) }}
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $project->progress_percentage }}%" 
                                             aria-valuenow="{{ $project->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $project->progress_percentage }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <p>No projects found. <a href="{{ route('projects.create') }}">Create your first project</a> to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($projects->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $projects->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@stop
