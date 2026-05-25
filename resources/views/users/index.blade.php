@extends('adminlte::page')

@section('title', 'Users Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Users Management</h1>
        @can('create-users')
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create User
        </a>
        @endcan
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Created</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if($user->hasRole('Super Admin'))
                                <span class="badge badge-danger">Super Admin</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge badge-{{ $role === 'Super Admin' ? 'danger' : ($role === 'Admin' ? 'warning' : ($role === 'Manager' ? 'info' : ($role === 'Accountant' ? 'success' : ($role === 'Cashier' ? 'secondary' : 'primary')))) }}">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-right">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('edit-users')
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @can('delete-users')
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $users->links() }}
    </div>
</div>
@stop
