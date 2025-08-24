@extends('adminlte::page')

@section('title', 'Reports')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Reports</h1>
        <a href="{{ route('reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Report
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Filter Section -->
    <div class="col-12">
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filters
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search reports...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type">Report Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">All Types</option>
                                    @foreach($reportTypes as $key => $value)
                                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reports List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i> All Reports
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $reports->total() }} total</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($reports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Format</th>
                                    <th>Scheduled</th>
                                    <th>Last Generated</th>
                                    <th>Generation Count</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $report->name }}</strong>
                                            @if($report->description)
                                                <br><small class="text-muted">{{ Str::limit($report->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $reportTypes[$report->type] ?? $report->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $report->status === 'active' ? 'success' : ($report->status === 'archived' ? 'secondary' : 'warning') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">
                                            {{ ucfirst($report->format) }}
                                            @if($report->chart_type)
                                                ({{ ucfirst($report->chart_type) }})
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($report->is_scheduled)
                                            <span class="badge badge-primary">
                                                <i class="fas fa-clock"></i> {{ ucfirst($report->schedule_frequency) }}
                                            </span>
                                            @if($report->next_generation_at)
                                                <br><small class="text-muted">
                                                    Next: {{ $report->next_generation_at->format('M d, Y H:i') }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">Manual</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->last_generated_at)
                                            {{ $report->last_generated_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-dark">{{ $report->generation_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('reports.show', $report) }}" class="btn btn-info" title="View Report">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('reports.edit', $report) }}" class="btn btn-warning" title="Edit Report">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('reports.export.pdf', $report) }}">
                                                        <i class="fas fa-file-pdf text-danger"></i> PDF
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('reports.export.excel', $report) }}">
                                                        <i class="fas fa-file-excel text-success"></i> Excel
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('reports.export.csv', $report) }}">
                                                        <i class="fas fa-file-csv text-info"></i> CSV
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <form method="POST" action="{{ route('reports.generate', $report) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-play text-success"></i> Generate Now
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('reports.duplicate', $report) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-copy text-info"></i> Duplicate
                                                        </button>
                                                    </form>
                                                    <div class="dropdown-divider"></div>
                                                    <form method="POST" action="{{ route('reports.destroy', $report) }}" 
                                                          onsubmit="return confirm('Are you sure you want to delete this report?')" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer">
                        {{ $reports->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Reports Found</h4>
                        <p class="text-muted">Get started by creating your first report.</p>
                        <a href="{{ route('reports.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Report
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $reports->where('status', 'active')->count() }}</h3>
                <p>Active Reports</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $reports->where('is_scheduled', true)->count() }}</h3>
                <p>Scheduled Reports</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $reports->sum('generation_count') }}</h3>
                <p>Total Generations</p>
            </div>
            <div class="icon">
                <i class="fas fa-play"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $reports->where('status', 'draft')->count() }}</h3>
                <p>Draft Reports</p>
            </div>
            <div class="icon">
                <i class="fas fa-edit"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.btn-group-sm .btn {
    font-size: 0.75rem;
}
.table td {
    vertical-align: middle;
}
.small-box {
    margin-bottom: 20px;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#type, #status').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Tooltip initialization
    $('[title]').tooltip();
});
</script>
@stop