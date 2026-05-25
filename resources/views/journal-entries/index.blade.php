@extends('adminlte::page')

@section('title', 'Journal Entries')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Journal Entries</h1>
        <a href="{{ route('journal-entries.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Entry</a>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('journal-entries.index') }}" class="d-flex gap-2">
            <select name="source_type" class="form-control form-control-sm" style="max-width:180px">
                <option value="">All Sources</option>
                @foreach($sourceTypes as $value => $label)
                    <option value="{{ $value }}" {{ request('source_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Description</th>
                    <th>Source</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th>Status</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->date->format('Y-m-d') }}</td>
                    <td><a href="{{ route('journal-entries.show', $entry) }}">{{ $entry->reference_number }}</a></td>
                    <td>{{ Str::limit($entry->description, 50) }}</td>
                    <td>
                        @if($entry->source_type)
                            <span class="badge badge-secondary">{{ str_replace('_', ' ', ucfirst($entry->source_type)) }}</span>
                        @else
                            <span class="badge badge-light">Manual</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($entry->total_debit, 2) }}</td>
                    <td class="text-right">{{ number_format($entry->total_credit, 2) }}</td>
                    <td>
                        @if($entry->is_posted)
                            <span class="badge badge-success">Posted</span>
                        @else
                            <span class="badge badge-warning">Draft</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        @if($entry->is_posted)
                            <form method="POST" action="{{ route('journal-entries.reverse', $entry) }}" style="display:inline"
                                  onsubmit="return confirm('Are you sure you want to reverse this entry?')">
                                @csrf
                                <button class="btn btn-xs btn-warning" title="Reverse"><i class="fas fa-undo"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted p-3">No journal entries found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $entries->links() }}</div>
</div>
@stop
