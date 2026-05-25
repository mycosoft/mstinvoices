@extends('adminlte::page')

@section('title', 'Journal Report')

@section('content_header')
    <h1>Journal Report</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('financial-reports.journal-report') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <label class="mb-0">From:</label>
            <input type="date" name="from_date" class="form-control form-control-sm" style="max-width:150px" value="{{ request('from_date') }}">
            <label class="mb-0">To:</label>
            <input type="date" name="to_date" class="form-control form-control-sm" style="max-width:150px" value="{{ request('to_date') }}">
            <select name="source_type" class="form-control form-control-sm" style="max-width:180px">
                @foreach($sourceTypes as $value => $label)
                    <option value="{{ $value }}" {{ request('source_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Filter</button>
        </form>
    </div>
    <div class="card-body p-0">
        @forelse($entries as $entry)
        <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="d-flex justify-content-between">
                <strong>{{ $entry->reference_number }}</strong>
                <span class="text-muted">{{ $entry->date->format('Y-m-d') }}</span>
            </div>
            <p class="mb-1">{{ $entry->description }} 
                <span class="badge badge-info">{{ $sourceTypes[$entry->source_type] ?? $entry->source_type }}</span>
                @if($entry->is_posted)<span class="badge badge-success">Posted</span>@endif
            </p>
            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entry->lines as $line)
                    <tr>
                        <td>{{ $line->account->code }} - {{ $line->account->name }}</td>
                        <td class="text-right">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                        <td class="text-right">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td class="text-right">Totals</td>
                        <td class="text-right">{{ number_format($entry->total_debit, 2) }}</td>
                        <td class="text-right">{{ number_format($entry->total_credit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @empty
        <div class="text-center py-4">No journal entries found.</div>
        @endforelse
    </div>
    @if($entries->hasPages())
    <div class="card-footer">
        {{ $entries->links() }}
    </div>
    @endif
</div>
@stop
