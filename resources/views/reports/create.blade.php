@extends('adminlte::page')

@section('title', 'Create Report')

@section('content_header')
    <h1>Create New Report</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('reports.store') }}" method="POST">
            @csrf
            
            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Basic Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Report Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Report Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Select Report Type</option>
                                    @foreach($reportTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="format">Display Format <span class="text-danger">*</span></label>
                                <select class="form-control @error('format') is-invalid @enderror" 
                                        id="format" name="format" required>
                                    <option value="table" {{ old('format', 'table') == 'table' ? 'selected' : '' }}>Table</option>
                                    <option value="chart" {{ old('format') == 'chart' ? 'selected' : '' }}>Chart</option>
                                    <option value="summary" {{ old('format') == 'summary' ? 'selected' : '' }}>Summary</option>
                                </select>
                                @error('format')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="chart_type">Chart Type</label>
                                <select class="form-control @error('chart_type') is-invalid @enderror" 
                                        id="chart_type" name="chart_type">
                                    <option value="">Select Chart Type</option>
                                    <option value="bar" {{ old('chart_type') == 'bar' ? 'selected' : '' }}>Bar Chart</option>
                                    <option value="line" {{ old('chart_type') == 'line' ? 'selected' : '' }}>Line Chart</option>
                                    <option value="pie" {{ old('chart_type') == 'pie' ? 'selected' : '' }}>Pie Chart</option>
                                    <option value="doughnut" {{ old('chart_type') == 'doughnut' ? 'selected' : '' }}>Doughnut Chart</option>
                                    <option value="area" {{ old('chart_type') == 'area' ? 'selected' : '' }}>Area Chart</option>
                                </select>
                                @error('chart_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Required when format is "Chart"</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Date Range Configuration -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar"></i> Date Range Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_range_type">Date Range Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('date_range_type') is-invalid @enderror" 
                                        id="date_range_type" name="date_range_type" required>
                                    @foreach($dateRangeTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('date_range_type', 'this_month') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('date_range_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="custom_date_from" style="display: none;">
                            <div class="form-group">
                                <label for="date_from">From Date</label>
                                <input type="date" class="form-control @error('date_from') is-invalid @enderror" 
                                       id="date_from" name="date_from" value="{{ old('date_from') }}">
                                @error('date_from')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="custom_date_to" style="display: none;">
                            <div class="form-group">
                                <label for="date_to">To Date</label>
                                <input type="date" class="form-control @error('date_to') is-invalid @enderror" 
                                       id="date_to" name="date_to" value="{{ old('date_to') }}">
                                @error('date_to')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scheduling Configuration -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Scheduling Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_scheduled" 
                                           name="is_scheduled" value="1" {{ old('is_scheduled') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_scheduled">
                                        Enable Automatic Report Generation
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="scheduling_options" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_frequency">Frequency</label>
                                    <select class="form-control @error('schedule_frequency') is-invalid @enderror" 
                                            id="schedule_frequency" name="schedule_frequency">
                                        <option value="">Select Frequency</option>
                                        <option value="daily" {{ old('schedule_frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ old('schedule_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('schedule_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('schedule_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="yearly" {{ old('schedule_frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                    @error('schedule_frequency')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_time">Generation Time</label>
                                    <input type="time" class="form-control @error('schedule_time') is-invalid @enderror" 
                                           id="schedule_time" name="schedule_time" value="{{ old('schedule_time', '09:00') }}">
                                    @error('schedule_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Email Configuration -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope"></i> Email Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="auto_email" 
                                           name="auto_email" value="1" {{ old('auto_email') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_email">
                                        Automatically Email Generated Reports
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="email_options" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email_recipients">Email Recipients</label>
                                    <textarea class="form-control @error('email_recipients') is-invalid @enderror" 
                                              id="email_recipients" name="email_recipients" rows="3" 
                                              placeholder="Enter email addresses separated by commas">{{ old('email_recipients') }}</textarea>
                                    @error('email_recipients')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Enter multiple email addresses separated by commas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Export Configuration -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-download"></i> Export Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Available Export Formats</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="export_pdf" 
                                                   name="export_formats[]" value="pdf" 
                                                   {{ in_array('pdf', old('export_formats', ['pdf'])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="export_pdf">
                                                <i class="fas fa-file-pdf text-danger"></i> PDF Export
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="export_excel" 
                                                   name="export_formats[]" value="excel" 
                                                   {{ in_array('excel', old('export_formats', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="export_excel">
                                                <i class="fas fa-file-excel text-success"></i> Excel Export
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="export_csv" 
                                                   name="export_formats[]" value="csv" 
                                                   {{ in_array('csv', old('export_formats', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="export_csv">
                                                <i class="fas fa-file-csv text-info"></i> CSV Export
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Create Report
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Show/hide custom date fields
    function toggleCustomDates() {
        const dateRangeType = $('#date_range_type').val();
        if (dateRangeType === 'custom') {
            $('#custom_date_from, #custom_date_to').show();
        } else {
            $('#custom_date_from, #custom_date_to').hide();
        }
    }
    
    $('#date_range_type').on('change', toggleCustomDates);
    toggleCustomDates(); // Initial check
    
    // Show/hide scheduling options
    function toggleScheduling() {
        if ($('#is_scheduled').is(':checked')) {
            $('#scheduling_options').show();
        } else {
            $('#scheduling_options').hide();
        }
    }
    
    $('#is_scheduled').on('change', toggleScheduling);
    toggleScheduling(); // Initial check
    
    // Show/hide email options
    function toggleEmail() {
        if ($('#auto_email').is(':checked')) {
            $('#email_options').show();
        } else {
            $('#email_options').hide();
        }
    }
    
    $('#auto_email').on('change', toggleEmail);
    toggleEmail(); // Initial check
    
    // Show/hide chart type based on format
    function toggleChartType() {
        const format = $('#format').val();
        if (format === 'chart') {
            $('#chart_type').closest('.form-group').show();
        } else {
            $('#chart_type').closest('.form-group').hide();
        }
    }
    
    $('#format').on('change', toggleChartType);
    toggleChartType(); // Initial check
    
    // Convert email recipients to array format
    $('#email_recipients').on('blur', function() {
        const emails = $(this).val().split(',').map(email => email.trim()).filter(email => email);
        // This will be handled on the server side
    });
});
</script>
@stop