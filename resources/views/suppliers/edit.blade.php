@extends('adminlte::page')

@section('title', 'Edit Supplier')

@section('content_header')
    <h1><a href="{{ route('suppliers.index') }}">Suppliers</a> / Edit {{ $supplier->name }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $supplier->company_name) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tax Number</label>
                        <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $supplier->tax_number) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control">{{ old('address', $supplier->address) }}</textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $supplier->city) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>State/Province</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $supplier->state) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $supplier->postal_code) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $supplier->country) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Payment Terms (days)</label>
                        <input type="number" name="payment_terms" class="form-control" value="{{ old('payment_terms', $supplier->payment_terms ?? 30) }}" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Supplier</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop
