@extends('adminlte::page')

@section('title', 'New Journal Entry')

@section('content_header')
    <h1><a href="{{ route('journal-entries.index') }}">Journal Entries</a> / New Entry</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('journal-entries.store') }}" id="je-form">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Describe this journal entry">
                    </div>
                </div>
            </div>

            <h5 class="mt-3">Journal Entry Lines</h5>
            <div id="lines-container">
                <table class="table table-bordered" id="lines-table">
                    <thead>
                        <tr>
                            <th style="width:35%">Account</th>
                            <th style="width:20%">Debit</th>
                            <th style="width:20%">Credit</th>
                            <th style="width:20%">Description</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="lines-body">
                        <tr>
                            <td>
                                <select name="lines[0][account_id]" class="form-control form-control-sm" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="lines[0][debit]" class="form-control form-control-sm line-debit" step="0.01" min="0" value="0"></td>
                            <td><input type="number" name="lines[0][credit]" class="form-control form-control-sm line-credit" step="0.01" min="0" value="0"></td>
                            <td><input type="text" name="lines[0][description]" class="form-control form-control-sm"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="lines[1][account_id]" class="form-control form-control-sm" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="lines[1][debit]" class="form-control form-control-sm line-debit" step="0.01" min="0" value="0"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control form-control-sm line-credit" step="0.01" min="0" value="0"></td>
                            <td><input type="text" name="lines[1][description]" class="form-control form-control-sm"></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td class="text-right">Totals</td>
                            <td id="total-debit">0.00</td>
                            <td id="total-credit">0.00</td>
                            <td colspan="2">
                                <span id="balance-status" class="badge badge-danger">Unbalanced</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLine()"><i class="fas fa-plus"></i> Add Line</button>

            @error('lines')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror

            <div class="form-group mt-3">
                <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Post Entry</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
let lineIndex = 2;
const accountOptions = `@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>@endforeach`;

function addLine() {
    const row = `<tr>
        <td><select name="lines[${lineIndex}][account_id]" class="form-control form-control-sm" required><option value="">Select Account</option>${accountOptions}</select></td>
        <td><input type="number" name="lines[${lineIndex}][debit]" class="form-control form-control-sm line-debit" step="0.01" min="0" value="0"></td>
        <td><input type="number" name="lines[${lineIndex}][credit]" class="form-control form-control-sm line-credit" step="0.01" min="0" value="0"></td>
        <td><input type="text" name="lines[${lineIndex}][description]" class="form-control form-control-sm"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();calculateTotals()"><i class="fas fa-trash"></i></button></td>
    </tr>`;
    document.getElementById('lines-body').insertAdjacentHTML('beforeend', row);
    lineIndex++;
    bindCalculation();
}

function calculateTotals() {
    let totalDebit = 0, totalCredit = 0;
    document.querySelectorAll('.line-debit').forEach(el => totalDebit += parseFloat(el.value) || 0);
    document.querySelectorAll('.line-credit').forEach(el => totalCredit += parseFloat(el.value) || 0);
    document.getElementById('total-debit').textContent = totalDebit.toFixed(2);
    document.getElementById('total-credit').textContent = totalCredit.toFixed(2);
    const diff = Math.abs(totalDebit - totalCredit);
    const status = document.getElementById('balance-status');
    if (diff < 0.01 && (totalDebit > 0 || totalCredit > 0)) {
        status.className = 'badge badge-success';
        status.textContent = 'Balanced';
    } else {
        status.className = 'badge badge-danger';
        status.textContent = 'Unbalanced (Diff: ' + diff.toFixed(2) + ')';
    }
}

function bindCalculation() {
    document.querySelectorAll('.line-debit, .line-credit').forEach(el => {
        el.removeEventListener('input', calculateTotals);
        el.addEventListener('input', calculateTotals);
    });
}
bindCalculation();
</script>
@endpush
@stop
