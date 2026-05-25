@extends('adminlte::page')

@section('title', 'Suppliers')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Suppliers</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Supplier
        </a>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('suppliers.index') }}" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:250px"
                   placeholder="Search suppliers..." value="{{ request('search') }}">
            <select name="status" class="form-control form-control-sm" style="max-width:150px">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td><a href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a></td>
                    <td>{{ $supplier->company_name ?? '-' }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td>{{ $supplier->phone ?? '-' }}</td>
                    <td>
                        @if($supplier->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display:inline"
                              onsubmit="return confirm('Delete this supplier?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@stop
