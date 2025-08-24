@extends('adminlte::page')

@section('title', 'Reports Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tachometer-alt text-primary"></i> Reports Dashboard</h1>
        <div class="btn-group">
            <a href="{{ route('reports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Report
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list"></i> All Reports
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_reports'] }}</h3>
                <p>Total Reports</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <a href="{{ route('reports.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['active_reports'] }}</h3>
                <p>Active Reports</p>
            </div>
            <div class="icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <a href="{{ route('reports.index', ['status' => 'active']) }}" class="small-box-footer">
                View Active <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['scheduled_reports'] }}</h3>
                <p>Scheduled Reports</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="#scheduled-reports" class="small-box-footer">
                View Schedule <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['draft_reports'] }}</h3>
                <p>Draft Reports</p>
            </div>
            <div class="icon">
                <i class="fas fa-edit"></i>
            </div>
            <a href="{{ route('reports.index', ['status' => 'draft']) }}" class="small-box-footer">
                View Drafts <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Business Analytics Section -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie text-info"></i> Business Analytics Overview
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">This Month vs Last Month</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Revenue Analytics -->
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $businessInsights['revenue_growth'] >= 0 ? 'success' : 'danger' }} elevation-1">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">This Month Revenue</span>
                                <span class="info-box-number">UGX {{ number_format($businessInsights['current_revenue'], 0) }}</span>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $businessInsights['revenue_growth'] >= 0 ? 'success' : 'danger' }}" 
                                         style="width: {{ min(abs($businessInsights['revenue_growth']), 100) }}%"></div>
                                </div>
                                <span class="progress-description">
                                    <i class="fas fa-{{ $businessInsights['revenue_growth'] >= 0 ? 'arrow-up text-success' : 'arrow-down text-danger' }}"></i>
                                    {{ number_format(abs($businessInsights['revenue_growth']), 1) }}% vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Invoice Volume -->
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $businessInsights['invoice_growth'] >= 0 ? 'primary' : 'warning' }} elevation-1">
                                <i class="fas fa-file-invoice"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">This Month Invoices</span>
                                <span class="info-box-number">{{ number_format($businessInsights['current_invoices']) }}</span>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $businessInsights['invoice_growth'] >= 0 ? 'primary' : 'warning' }}" 
                                         style="width: {{ min(abs($businessInsights['invoice_growth']), 100) }}%"></div>
                                </div>
                                <span class="progress-description">
                                    <i class="fas fa-{{ $businessInsights['invoice_growth'] >= 0 ? 'arrow-up text-primary' : 'arrow-down text-warning' }}"></i>
                                    {{ number_format(abs($businessInsights['invoice_growth']), 1) }}% vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collection Rate -->
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $businessInsights['collection_rate'] >= 80 ? 'success' : ($businessInsights['collection_rate'] >= 60 ? 'warning' : 'danger') }} elevation-1">
                                <i class="fas fa-percentage"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Collection Rate (30d)</span>
                                <span class="info-box-number">{{ number_format($businessInsights['collection_rate'], 1) }}%</span>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $businessInsights['collection_rate'] >= 80 ? 'success' : ($businessInsights['collection_rate'] >= 60 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $businessInsights['collection_rate'] }}%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ $businessInsights['collection_rate'] >= 80 ? 'Excellent' : ($businessInsights['collection_rate'] >= 60 ? 'Good' : 'Needs attention') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overdue Amount -->
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $businessInsights['overdue_count'] == 0 ? 'success' : ($businessInsights['overdue_count'] <= 5 ? 'warning' : 'danger') }} elevation-1">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Overdue Amount</span>
                                <span class="info-box-number">UGX {{ number_format($businessInsights['overdue_amount'], 0) }}</span>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $businessInsights['overdue_count'] == 0 ? 'success' : ($businessInsights['overdue_count'] <= 5 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $businessInsights['overdue_count'] == 0 ? 100 : min($businessInsights['overdue_count'] * 10, 100) }}%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ $businessInsights['overdue_count'] }} overdue invoices
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Insights Row -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="description-block">
                            <span class="description-percentage text-primary">
                                <i class="fas fa-users"></i>
                            </span>
                            <h5 class="description-header">{{ $businessInsights['active_clients'] }}/{{ $businessInsights['total_clients'] }}</h5>
                            <span class="description-text">ACTIVE CLIENTS THIS MONTH</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-trophy"></i>
                            </span>
                            <h5 class="description-header">{{ $businessInsights['top_client']->name ?? 'N/A' }}</h5>
                            <span class="description-text">TOP CLIENT THIS MONTH</span>
                            @if($businessInsights['top_client'])
                            <small class="text-muted d-block">UGX {{ number_format($businessInsights['top_client']->total_revenue, 0) }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="description-block">
                            <span class="description-percentage text-info">
                                <i class="fas fa-calculator"></i>
                            </span>
                            @php
                                $avgInvoice = $businessInsights['current_invoices'] > 0 ? $businessInsights['current_revenue'] / $businessInsights['current_invoices'] : 0;
                            @endphp
                            <h5 class="description-header">UGX {{ number_format($avgInvoice, 0) }}</h5>
                            <span class="description-text">AVG INVOICE VALUE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Analytics Cards Row -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-donut text-primary"></i> Report Type Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="reportTypeChart" style="height: 200px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line text-success"></i> Monthly Generation Trend
                </h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrendChart" style="height: 200px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Generation Activity Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-area"></i> Report Generation Activity (Last 30 Days)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('reports.quick.revenue') }}" class="btn btn-outline-success btn-block">
                        <i class="fas fa-dollar-sign"></i> Revenue Report
                    </a>
                    <a href="{{ route('reports.quick.client') }}" class="btn btn-outline-info btn-block">
                        <i class="fas fa-users"></i> Client Report
                    </a>
                    <a href="{{ route('reports.quick.monthly') }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-calendar-alt"></i> Monthly Report
                    </a>
                    <a href="{{ route('reports.quick.yearly') }}" class="btn btn-outline-warning btn-block">
                        <i class="fas fa-calendar"></i> Yearly Report
                    </a>
                    <hr>
                    <button type="button" class="btn btn-outline-secondary btn-block" onclick="runScheduledReports()">
                        <i class="fas fa-sync"></i> Run All Scheduled Reports
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Scheduled Reports -->
    <div class="col-md-6" id="scheduled-reports">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-check"></i> Upcoming Scheduled Reports
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $upcomingReports->count() }} upcoming</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($upcomingReports->count() > 0)
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-striped m-0">
                            <thead>
                                <tr>
                                    <th>Report</th>
                                    <th>Type</th>
                                    <th>Next Run</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingReports as $report)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $report->name }}</strong>
                                            <br><small class="text-muted">{{ ucfirst($report->schedule_frequency) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst(str_replace('_', ' ', $report->type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $report->next_generation_at->format('M d, Y') }}</strong>
                                            <br><small class="text-muted">{{ $report->next_generation_at->format('H:i A') }}</small>
                                            <br><small class="text-{{ $report->next_generation_at->isPast() ? 'danger' : ($report->next_generation_at->isToday() ? 'warning' : 'muted') }}\">\n                                                {{ $report->next_generation_at->diffForHumans() }}\n                                            </small>\n                                        </div>\n                                    </td>\n                                    <td>\n                                        <div class=\"btn-group-vertical btn-group-sm\">\n                                            <a href=\"{{ route('reports.show', $report) }}\" class=\"btn btn-outline-primary\" title=\"View Report\">\n                                                <i class=\"fas fa-eye\"></i>\n                                            </a>\n                                            <form method=\"POST\" action=\"{{ route('reports.test-scheduled', $report) }}\" style=\"display: inline;\">\n                                                @csrf\n                                                <button type=\"submit\" class=\"btn btn-outline-success\" title=\"Test Run\">\n                                                    <i class=\"fas fa-play\"></i>\n                                                </button>\n                                            </form>\n                                        </div>\n                                    </td>\n                                </tr>\n                                @endforeach\n                            </tbody>\n                        </table>\n                    </div>\n                @else\n                    <div class=\"p-3 text-center text-muted\">\n                        <i class=\"fas fa-calendar-times fa-2x mb-2\"></i>\n                        <p>No scheduled reports configured.</p>\n                        <a href=\"{{ route('reports.create') }}\" class=\"btn btn-sm btn-primary\">\n                            <i class=\"fas fa-plus\"></i> Create Scheduled Report\n                        </a>\n                    </div>\n                @endif\n            </div>\n        </div>\n    </div>\n\n    <!-- Recently Generated Reports -->\n    <div class=\"col-md-6\">\n        <div class=\"card\">\n            <div class=\"card-header\">\n                <h3 class=\"card-title\">\n                    <i class=\"fas fa-history\"></i> Recently Generated Reports\n                </h3>\n                <div class=\"card-tools\">\n                    <span class=\"badge badge-success\">{{ $recentReports->count() }} recent</span>\n                </div>\n            </div>\n            <div class=\"card-body p-0\">\n                @if($recentReports->count() > 0)\n                    <div class=\"table-responsive\" style=\"max-height: 400px;\">\n                        <table class=\"table table-striped m-0\">\n                            <thead>\n                                <tr>\n                                    <th>Report</th>\n                                    <th>Type</th>\n                                    <th>Generated</th>\n                                    <th>Actions</th>\n                                </tr>\n                            </thead>\n                            <tbody>\n                                @foreach($recentReports as $report)\n                                <tr>\n                                    <td>\n                                        <div>\n                                            <strong>{{ $report->name }}</strong>\n                                            <br><small class=\"text-muted\">\n                                                Generated {{ $report->generation_count }} times\n                                            </small>\n                                        </div>\n                                    </td>\n                                    <td>\n                                        <span class=\"badge badge-secondary\">\n                                            {{ ucfirst(str_replace('_', ' ', $report->type)) }}\n                                        </span>\n                                    </td>\n                                    <td>\n                                        <div>\n                                            <strong>{{ $report->last_generated_at->format('M d, Y') }}</strong>\n                                            <br><small class=\"text-muted\">{{ $report->last_generated_at->format('H:i A') }}</small>\n                                            <br><small class=\"text-muted\">\n                                                {{ $report->last_generated_at->diffForHumans() }}\n                                            </small>\n                                        </div>\n                                    </td>\n                                    <td>\n                                        <div class=\"btn-group-vertical btn-group-sm\">\n                                            <a href=\"{{ route('reports.show', $report) }}\" class=\"btn btn-outline-primary\" title=\"View Report\">\n                                                <i class=\"fas fa-eye\"></i>\n                                            </a>\n                                            <form method=\"POST\" action=\"{{ route('reports.generate', $report) }}\" style=\"display: inline;\">\n                                                @csrf\n                                                <button type=\"submit\" class=\"btn btn-outline-success\" title=\"Regenerate\">\n                                                    <i class=\"fas fa-sync\"></i>\n                                                </button>\n                                            </form>\n                                        </div>\n                                    </td>\n                                </tr>\n                                @endforeach\n                            </tbody>\n                        </table>\n                    </div>\n                @else\n                    <div class=\"p-3 text-center text-muted\">\n                        <i class=\"fas fa-chart-bar fa-2x mb-2\"></i>\n                        <p>No reports have been generated yet.</p>\n                        <a href=\"{{ route('reports.index') }}\" class=\"btn btn-sm btn-primary\">\n                            <i class=\"fas fa-play\"></i> Generate Your First Report\n                        </a>\n                    </div>\n                @endif\n            </div>\n        </div>\n    </div>\n\n    <!-- System Status -->\n    <div class=\"col-12\">\n        <div class=\"card\">\n            <div class=\"card-header\">\n                <h3 class=\"card-title\">\n                    <i class=\"fas fa-server\"></i> System Status & Information\n                </h3>\n            </div>\n            <div class=\"card-body\">\n                <div class=\"row\">\n                    <div class=\"col-md-3\">\n                        <div class=\"description-block\">\n                            <h5 class=\"description-header text-success\">✓</h5>\n                            <span class=\"description-text\">REPORTS ENGINE</span>\n                        </div>\n                    </div>\n                    <div class=\"col-md-3\">\n                        <div class=\"description-block border-right\">\n                            <h5 class=\"description-header text-{{ function_exists('mail') ? 'success' : 'danger' }}\">{{ function_exists('mail') ? '✓' : '✗' }}</h5>\n                            <span class=\"description-text\">EMAIL SYSTEM</span>\n                        </div>\n                    </div>\n                    <div class=\"col-md-3\">\n                        <div class=\"description-block border-right\">\n                            <h5 class=\"description-header text-success\">✓</h5>\n                            <span class=\"description-text\">PDF GENERATION</span>\n                        </div>\n                    </div>\n                    <div class=\"col-md-3\">\n                        <div class=\"description-block\">\n                            <h5 class=\"description-header text-info\">{{ now()->format('H:i') }}</h5>\n                            <span class=\"description-text\">SERVER TIME</span>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"mt-3\">\n                    <small class=\"text-muted\">\n                        <i class=\"fas fa-info-circle\"></i> \n                        Scheduled reports run automatically every hour. Use the \"Test Run\" button to manually test report generation and email delivery.\n                    </small>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n@stop\n\n@section('css')\n<style>\n.small-box {\n    margin-bottom: 20px;\n}\n.description-block {\n    margin: 0;\n    text-align: center;\n    padding: 15px;\n}\n.border-right {\n    border-right: 1px solid #dee2e6;\n}\n.btn-block {\n    width: 100%;\n    margin-bottom: 10px;\n}\n.d-grid {\n    display: grid;\n}\n.gap-2 {\n    gap: 0.5rem;\n}\n</style>\n@stop\n\n@section('js')\n<script src=\"https://cdn.jsdelivr.net/npm/chart.js\"></script>\n<script>\n$(document).ready(function() {\n    initializeActivityChart();
    initializeReportTypeChart();
    initializeMonthlyTrendChart();
});

function initializeActivityChart() {
    const ctx = document.getElementById('activityChart');
    if (!ctx) return;
    
    const generationData = @json($generationStats);
    
    // Create date range for last 30 days
    const dates = [];
    const counts = [];
    const today = new Date();
    
    for (let i = 29; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);
        const dateString = date.toISOString().split('T')[0];
        
        dates.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        
        const dayData = generationData.find(item => item.date === dateString);
        counts.push(dayData ? dayData.count : 0);
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Reports Generated',
                data: counts,
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return context.parsed.y + ' report' + (context.parsed.y !== 1 ? 's' : '') + ' generated';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [2, 2]
                    },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Math.floor(value) === value ? value : '';
                        }
                    }
                }
            }
        }
    });
}

function initializeReportTypeChart() {
    const ctx = document.getElementById('reportTypeChart');
    if (!ctx) return;
    
    const reportTypes = @json(array_keys($reportTypeStats ?? []));
    const reportCounts = @json(array_values($reportTypeStats ?? []));
    
    if (reportTypes.length === 0) {
        ctx.getContext('2d').fillText('No reports created yet', 10, 50);
        return;
    }
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: reportTypes.map(type => type.replace('_', ' ').toUpperCase()),
            datasets: [{
                data: reportCounts,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#FF6384',
                    '#C9CBCF'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

function initializeMonthlyTrendChart() {
    const ctx = document.getElementById('monthlyTrendChart');
    if (!ctx) return;
    
    const monthlyTrendData = @json($monthlyTrend ?? []);
    
    if (monthlyTrendData.length === 0) {
        ctx.getContext('2d').fillText('No generation history yet', 10, 50);
        return;
    }
    
    const labels = monthlyTrendData.map(item => {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return monthNames[item.month - 1] + ' ' + item.year;
    });
    
    const counts = monthlyTrendData.map(item => item.count);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Reports Generated',
                data: counts,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' report' + (context.parsed.y !== 1 ? 's' : '') + ' generated';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Math.floor(value) === value ? value : '';
                        }
                    }
                }
            }
        }
    });
}

function runScheduledReports() {
    if (confirm('This will run all scheduled reports now. Continue?')) {
        // Show loading indicator
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i> Running...';
        button.disabled = true;
        
        // Make AJAX request to run scheduled reports
        $.ajax({
            url: '/artisan/reports:generate-scheduled',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
            },
            success: function(response) {
                alert('Scheduled reports have been executed successfully!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error running scheduled reports: ' + (xhr.responseJSON?.message || 'Unknown error'));
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }
}

// Auto refresh the page every 5 minutes to keep data current
setInterval(function() {
    location.reload();
}, 300000);

</script>\n@stop