@extends('adminlte::page')

@section('title', $journalEntry->reference_number)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><a href="{{ route('journal-entries.index') }}">Journal Entries</a> / {{ $journalEntry->reference_number }}</h1>
        <div>
            @if($journalEntry->is_posted)
                <form method="POST" action="{{ route('journal-entries.reverse', $journalEntry) }}" style="display:inline"
                      onsubmit="return confirm('Are you sure you want to reverse this entry?')">
                    @csrf
                    <button class="btn btn-warning btn-sm"><i class="fas fa-undo"></i> Reverse Entry</button>
                </form>
            @endif
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><strong>Entry Details</strong></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td>Reference</td><td><code>{{ $journalEntry->reference_number }}</code></td></tr>
                    <tr><td>Date</td><td>{{ $journalEntry->date->format('Y-m-d') }}</td></tr>
                    <tr><td>Description</td><td>{{ $journalEntry->description ?: '-' }}</td></tr>
                    <tr><td>Source</td><td>{{ $journalEntry->source_type ? str_replace('_', ' ', ucfirst($journalEntry->source_type)) : 'Manual' }}</td></tr>
                    <tr><td>Posted</td><td>{{ $journalEntry->posted_at ? $journalEntry->posted_at->format('Y-m-d H:i') : 'No' }}</td></tr>
                    <tr><td>Status</td><td>
                        @if($journalEntry->is_posted)
                            <span class="badge badge-success">Posted</span>
                        @else
                            <span class="badge badge-warning">Draft</span>
                        @endif
                    </td></tr>
                    <tr><td>Total Debit</td><td class="font-weight-bold">{{ number_format($journalEntry->total_debit, 2) }}</td></tr>
                    <tr><td>Total Credit</td><td class="font-weight-bold">{{ number_format($journalEntry->total_credit, 2) }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><strong>Entry Lines</strong></div>
            <div class="card-body p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journalEntry->lines as $line)
                        <tr>
                            <td>
                                <a href="{{ route('accounts.show', $line->account) }}">
                                    {{ $line->account->code }} - {{ $line->account->name }}
                                </a>
                            </td>
                            <td class="text-right">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                            <td class="text-right">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                            <td>{{ $line->description ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td>Total</td>
                            <td class="text-right">{{ number_format($journalEntry->total_debit, 2) }}</td>
                            <td class="text-right">{{ number_format($journalEntry->total_credit, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
