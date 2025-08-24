@extends('adminlte::page')

@section('title', 'Report: ' . $report->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $report->name }}</h1>
        <div class="btn-group">
            <a href="{{ route('reports.edit', $report) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-download"></i> Export
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('reports.export.pdf', $report) }}" target="_blank">
                        <i class="fas fa-file-pdf text-danger"></i> Export as PDF
                    </a>
                    <a class="dropdown-item" href="{{ route('reports.export.excel', $report) }}">
                        <i class="fas fa-file-excel text-success"></i> Export as Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('reports.export.csv', $report) }}">
                        <i class="fas fa-file-csv text-info"></i> Export as CSV
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('reports.generate', $report) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-sync"></i> Regenerate
                </button>
            </form>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Report Header Info -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Report Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Type:</strong><br>
                        <span class="badge badge-info">{{ $reportTypes[$report->type] ?? $report->type }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong><br>
                        <span class="badge badge-{{ $report->status === 'active' ? 'success' : ($report->status === 'archived' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Format:</strong><br>
                        <span class="badge badge-light">{{ ucfirst($report->format) }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Date Range:</strong><br>
                        @if($report->date_range_type === 'custom')
                            {{ $report->date_from->format('M d, Y') }} - {{ $report->date_to->format('M d, Y') }}
                        @else
                            {{ $dateRangeTypes[$report->date_range_type] ?? $report->date_range_type }}
                        @endif
                    </div>
                </div>
                @if($report->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Description:</strong><br>
                            {{ $report->description }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Report Statistics -->
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $report->generation_count }}</h3>
                <p>Times Generated</p>
            </div>
            <div class="icon">
                <i class="fas fa-play"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $report->last_generated_at ? $report->last_generated_at->diffForHumans() : 'Never' }}</h3>
                <p>Last Generated</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $report->is_scheduled ? 'Yes' : 'No' }}</h3>
                <p>Scheduled</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $report->auto_email ? 'Yes' : 'No' }}</h3>
                <p>Auto Email</p>
            </div>
            <div class="icon">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
    </div>

    <!-- Report Content Based on Type -->
    @if($report->type === 'revenue')
        @include('reports.partials.revenue-report', ['data' => $reportData])
    @elseif($report->type === 'client')
        @include('reports.partials.client-report', ['data' => $reportData])
    @elseif($report->type === 'monthly' || $report->type === 'yearly')
        @include('reports.partials.time-based-report', ['data' => $reportData])
    @elseif($report->type === 'invoice_summary')
        @include('reports.partials.invoice-summary-report', ['data' => $reportData])
    @elseif($report->type === 'payment_summary')
        @include('reports.partials.payment-summary-report', ['data' => $reportData])
    @elseif($report->type === 'tax_summary')
        @include('reports.partials.tax-summary-report', ['data' => $reportData])
    @else
        @include('reports.partials.custom-report', ['data' => $reportData])
    @endif

    <!-- Scheduling Information -->
    @if($report->is_scheduled)
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i> Scheduling Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Frequency:</strong><br>
                        <span class="badge badge-primary">{{ ucfirst($report->schedule_frequency) }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Generation Time:</strong><br>
                        {{ $report->schedule_time ? $report->schedule_time->format('H:i') : 'Not set' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Next Generation:</strong><br>
                        {{ $report->next_generation_at ? $report->next_generation_at->format('M d, Y H:i') : 'Not scheduled' }}
                    </div>
                </div>
                @if($report->auto_email && $report->email_recipients)
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Email Recipients:</strong><br>
                        @foreach($report->email_recipients as $email)
                            <span class="badge badge-info">{{ $email }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@stop

@section('css')
<style>
.chart-container {
    position: relative;
    margin-bottom: 20px;
}

.table-responsive {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.info-box {
    margin-bottom: 20px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    transition: all 0.3s ease;
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,.15);
}

.info-box-icon {
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.report-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid #007bff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 0.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
}

.description-block {
    margin: 0;
    padding: 15px;
    text-align: center;
}

.description-percentage {
    font-size: 1.2em;
    font-weight: bold;
}

.description-header {
    font-size: 1.5em;
    margin: 10px 0 5px 0;
    font-weight: bold;
}

.description-text {
    font-size: 0.8em;
    text-transform: uppercase;
    font-weight: 600;
    color: #6c757d;
    letter-spacing: 0.5px;
}

.border-right {
    border-right: 1px solid #dee2e6;
}

.progress {
    height: 8px;
    margin-top: 5px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.6s ease;
}

.progress-description {
    font-size: 0.75em;
    color: #6c757d;
}

.badge {
    font-size: 0.75em;
    border-radius: 0.25rem;
    padding: 0.375rem 0.5rem;
}

.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody tr {
    transition: background-color 0.15s ease-in-out;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table td, .table th {
    vertical-align: middle;
    padding: 12px 8px;
}

.table tfoot {
    background-color: #f1f3f4;
    font-weight: 600;
}

.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border: none;
    border-radius: 0.375rem;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.375rem 0.375rem 0 0 !important;
    padding: 0.75rem 1rem;
}

.card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
}

.card-tools .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,123,255,.3);
}

.small-box {
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    transition: transform 0.2s ease;
}

.small-box:hover {
    transform: translateY(-2px);
}

.small-box .inner {
    padding: 15px;
}

.small-box .icon {
    position: absolute;
    top: auto;
    bottom: 5px;
    right: 5px;
    z-index: 0;
    font-size: 70px;
    color: rgba(0,0,0,0.15);
}

.elevation-1 {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .description-block {
        padding: 10px;
        margin-bottom: 15px;
    }
    
    .border-right {
        border-right: none;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    
    .chart-container {
        height: 300px !important;
    }
    
    .info-box-icon {
        width: 60px;
        height: 60px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

/* Animation for chart switching */
.chart-switch-animation {
    transition: opacity 0.3s ease;
}

.chart-switch-animation.switching {
    opacity: 0.5;
}

/* Print styles */
@media print {
    .card-tools,
    .btn,
    .btn-group {
        display: none !important;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #dee2e6;
    }
    
    .table {
        font-size: 12px;
    }
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script>
// Global chart variables
let revenueChart = null;
let revenueDonutChart = null;
let paymentStatusChart = null;

$(document).ready(function() {
    // Initialize all charts
    initializeCharts();
    
    // Initialize DataTables if available
    if ($.fn.DataTable) {
        $('#monthlyRevenueTable').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'pageLength': 12,
            'order': [[1, 'desc'], [0, 'desc']] // Sort by year desc, then month desc
        });
    }
    
    // Auto-refresh functionality for scheduled reports
    @if($report->is_scheduled)
        setInterval(function() {
            // Check if report needs refresh (every 5 minutes)
            checkReportStatus();
        }, 300000);
    @endif
});

function initializeCharts() {
    @if(isset($reportData['monthly_data']) && count($reportData['monthly_data']) > 0)
        initializeRevenueChart();
        initializeDonutChart();
        initializePaymentStatusChart();
    @endif
}

function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    const monthlyData = @json($reportData['monthly_data'] ?? []);
    
    const chartData = {
        labels: monthlyData.map(item => {
            const date = new Date(item.year, item.month - 1);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }),
        datasets: [
            {
                label: 'Total Revenue',
                data: monthlyData.map(item => item.total_revenue),
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            },
            {
                label: 'Paid Revenue',
                data: monthlyData.map(item => item.paid_revenue),
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            },
            {
                label: 'Pending Revenue',
                data: monthlyData.map(item => item.total_revenue - item.paid_revenue),
                backgroundColor: 'rgba(255, 206, 86, 0.1)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                borderDash: [5, 5],
                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }
        ]
    };
    
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return context.dataset.label + ': UGX ' + context.parsed.y.toLocaleString();
                        },
                        afterBody: function(context) {
                            const dataIndex = context[0].dataIndex;
                            const invoiceCount = monthlyData[dataIndex].invoice_count;
                            const avgInvoice = monthlyData[dataIndex].total_revenue / invoiceCount;
                            return [
                                'Invoices: ' + invoiceCount,
                                'Avg Invoice: UGX ' + avgInvoice.toLocaleString()
                            ];
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
                        maxRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [2, 2]
                    },
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#fff'
                }
            }
        }
    });
}

function initializeDonutChart() {
    const ctx = document.getElementById('revenueDonutChart');
    if (!ctx) return;
    
    const summary = @json($reportData['summary'] ?? []);
    
    revenueDonutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Paid Revenue', 'Pending Revenue'],
            datasets: [{
                data: [summary.paid_revenue, summary.pending_revenue],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 2,
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = summary.total_revenue;
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': UGX ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
}

function initializePaymentStatusChart() {
    const ctx = document.getElementById('paymentStatusChart');
    if (!ctx) return;
    
    const summary = @json($reportData['summary'] ?? []);
    const paymentRate = summary.total_revenue > 0 ? (summary.paid_revenue / summary.total_revenue) * 100 : 0;
    
    paymentStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [paymentRate, 100 - paymentRate],
                backgroundColor: [
                    paymentRate >= 80 ? '#28a745' : (paymentRate >= 50 ? '#ffc107' : '#dc3545'),
                    '#e9ecef'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            }
        },
        plugins: [{
            beforeDraw: function(chart) {
                const width = chart.width,
                      height = chart.height,
                      ctx = chart.ctx;
                
                ctx.restore();
                const fontSize = (height / 100).toFixed(2);
                ctx.font = fontSize + "em Arial";
                ctx.textBaseline = "middle";
                
                const text = paymentRate.toFixed(1) + "%",
                      textX = Math.round((width - ctx.measureText(text).width) / 2),
                      textY = height / 2;
                
                ctx.fillStyle = paymentRate >= 80 ? '#28a745' : (paymentRate >= 50 ? '#ffc107' : '#dc3545');
                ctx.fillText(text, textX, textY);
                
                // Add label
                ctx.font = (fontSize * 0.6) + "em Arial";
                const labelText = "Collection Rate";
                const labelX = Math.round((width - ctx.measureText(labelText).width) / 2);
                ctx.fillStyle = '#6c757d';
                ctx.fillText(labelText, labelX, textY + 20);
                ctx.save();
            }
        }]
    });
}

function switchChart(type) {
    if (revenueChart) {
        revenueChart.config.type = type;
        if (type === 'area') {
            revenueChart.config.type = 'line';
            revenueChart.data.datasets.forEach(dataset => {
                dataset.fill = true;
            });
        } else {
            revenueChart.data.datasets.forEach((dataset, index) => {
                dataset.fill = index === 0; // Only fill the first dataset for line charts
            });
        }
        revenueChart.update();
    }
}

function exportTableToCSV() {
    const table = document.getElementById('monthlyRevenueTable');
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length - 1; j++) { // Skip last column (actions)
            let text = cols[j].innerText.replace(/,/g, '');
            row.push('"' + text + '"');
        }
        csv.push(row.join(','));
    }
    
    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = 'revenue-report-' + new Date().toISOString().slice(0, 10) + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

function checkReportStatus() {
    // Implementation for checking if report needs refresh
    // This could make an AJAX call to check the report status
    console.log('Checking report status...');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'e':
                e.preventDefault();
                document.querySelector('.dropdown-toggle[data-toggle="dropdown"]').click();
                break;
            case 'r':
                e.preventDefault();
                document.querySelector('form[action*="generate"] button').click();
                break;
        }
    }
});
</script>
@stop