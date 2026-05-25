@extends('adminlte::page')

@section('title', 'Create Account')

@section('content_header')
    <h1><a href="{{ route('accounts.index') }}">Chart of Accounts</a> / New Account</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('accounts.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Account Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" id="account-type" required>
                            <option value="">Select Type</option>
                            @foreach($accountTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Subtype</label>
                        <input type="text" name="subtype" class="form-control" value="{{ old('subtype') }}" placeholder="e.g. current, non_current, operating">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Normal Balance <span class="text-danger">*</span></label>
                        <select name="normal_balance" class="form-control" id="normal-balance" required>
                            <option value="debit" {{ old('normal_balance') == 'debit' ? 'selected' : '' }}>Debit</option>
                            <option value="credit" {{ old('normal_balance') == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Opening Balance</label>
                        <input type="number" name="opening_balance" class="form-control" value="{{ old('opening_balance', 0) }}" step="0.01" min="0">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Parent Account</label>
                        <select name="parent_account_id" class="form-control">
                            <option value="">None (Top Level)</option>
                            @foreach($parentAccounts as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_account_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->code }} - {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Account</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
document.getElementById('account-type').addEventListener('change', function() {
    const type = this.value;
    const balanceSelect = document.getElementById('normal-balance');
    const normalBalances = { asset: 'debit', liability: 'credit', equity: 'credit', income: 'credit', expense: 'debit' };
    if (normalBalances[type]) {
        balanceSelect.value = normalBalances[type];
    }
});
</script>
@endpush
@stop
