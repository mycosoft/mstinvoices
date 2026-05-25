@extends('adminlte::page')

@section('title', 'User: ' . $user->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $user->name }}</h1>
        <div>
            @can('edit-users')
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
            <a href="{{ route('users.index') }}" class="btn btn-default">Back</a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profile</h3>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> #{{ $user->id }}</p>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Joined:</strong> {{ $user->created_at->format('F d, Y H:i') }}</p>

                <hr>
                <p><strong>Roles:</strong></p>
                @foreach($user->getRoleNames() as $role)
                    <span class="badge badge-primary">{{ $role }}</span>
                @endforeach

                @if($user->getPermissionNames()->count() > 0)
                <hr>
                <p><strong>Direct Permissions ({{ $user->getPermissionNames()->count() }}):</strong></p>
                @foreach($user->getPermissionNames() as $perm)
                    <span class="badge badge-secondary">{{ $perm }}</span>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $log->description === 'created' ? 'success' : ($log->description === 'updated' ? 'warning' : 'danger') }}">
                                        {{ $log->description }}
                                    </span>
                                </td>
                                <td>{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                                <td class="small text-muted">{{ Str::limit(json_encode($log->properties), 80) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No recent activity.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
