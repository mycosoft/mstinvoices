@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User: {{ $user->name }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Details</h3>
            </div>
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>

                    @can('assign-roles')
                    <div class="form-group">
                        <label>Assign Roles</label>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role-{{ $role->id }}" class="form-check-input" {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role-{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="form-group">
                        <label>Current Roles</label>
                        <div class="row">
                            @foreach($userRoles as $role)
                                <div class="col-md-4">
                                    <span class="badge badge-info">{{ $role }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endcan
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role Reference</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    @foreach($roles as $role)
                        <tr>
                            <td><strong>{{ $role->name }}</strong></td>
                            <td class="text-muted small">{{ $role->permissions->count() }} permissions</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@stop
