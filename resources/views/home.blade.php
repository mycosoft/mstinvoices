@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Dashboard</h1>
        <small class="text-muted">Welcome back, {{ Auth::user()->name }}!</small>
    </div>
@stop

@section('content')
<!-- First Row - Main Statistics Cards -->
<div class="row">
    <!-- Total Invoices -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalInvoices) }}</h3>
                <p>Total Invoices</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <a href="{{ route('invoices.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Total Revenue -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $settings->formatCurrency($totalRevenue) }}</h3>
                <p>Total Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <a href="{{ route('reports.quick.revenue') }}" class="small-box-footer">
                View Report <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <!-- This Month Revenue -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $settings->formatCurrency($thisMonthRevenue) }}</h3>
                <p>This Month Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <a href="{{ route('reports.quick.monthly') }}" class="small-box-footer">
                View Monthly <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Overdue Invoices -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($overdueInvoices) }}</h3>
                <p>Overdue Invoices</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('reports.quick.overdue-invoices') }}" class="small-box-footer">
                View Overdue <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Second Row - Additional Statistics Cards -->
<div class="row">
    <!-- Quotations Rate -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $quotationRate }}%</h3>
                <p>Quotation Acceptance Rate</p>
            </div>
            <div class="icon">
                <i class="fas fa-handshake"></i>
            </div>
            <a href="{{ route('quotations.index') }}" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Average Invoice Value -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $settings->formatCurrency($averageInvoiceValue) }}</h3>
                <p>Average Invoice</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <a href="{{ route('reports.index') }}" class="small-box-footer">
                View Analysis <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Pending Amount -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3>{{ $settings->formatCurrency($pendingAmount) }}</h3>
                <p>Pending Amount</p>
            </div>
            <div class="icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <a href="{{ route('reports.index') }}" class="small-box-footer">
                View Outstanding <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Draft Items -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-dark">
            <div class="inner">
                <h3>{{ number_format($draftInvoices + $draftQuotations) }}</h3>
                <p>Draft Items ({{ $draftInvoices }} Invoices, {{ $draftQuotations }} Quotations)</p>
            </div>
            <div class="icon">
                <i class="fas fa-edit"></i>
            </div>
            <a href="{{ route('invoices.index') }}" class="small-box-footer">
                View Drafts <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Charts and Analytics Row -->
<div class="row">
    <!-- Monthly Revenue Chart -->
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line text-primary"></i> Revenue Trend (Last 6 Months)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="height: 350px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Payment Status Distribution -->
    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie text-success"></i> Payment Status
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="paymentChart" style="height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Information Row -->
<div class="row">
    <!-- Recent Payments -->
    <div class="col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave text-info"></i> Recent Payments
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $recentPayments->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($recentPayments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $payment) }}" class="text-primary font-weight-bold">
                                        {{ $payment->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->client->name }}</td>
                                <td>
                                    <span class="text-success font-weight-bold">
                                        @if($payment->payment_status === 'partial')
                                            {{ $settings->formatCurrency($payment->paid_amount) }}
                                        @else
                                            {{ $settings->formatCurrency($payment->total_amount) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        @if($payment->paid_date)
                                            {{ $payment->paid_date->format('M d, Y') }}
                                        @elseif($payment->updated_at)
                                            {{ $payment->updated_at->format('M d, Y') }} (Partial)
                                        @else
                                            -
                                        @endif
                                    </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Recent Payments</h5>
                    <p class="text-muted">Payments from the last 7 days will appear here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quotations Accepted -->
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-handshake text-success"></i> Quotations Accepted
                </h3>
                <div class="card-tools">
                    <span class="badge badge-success">{{ $recentAcceptedQuotations->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($recentAcceptedQuotations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Quotation #</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Accepted Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAcceptedQuotations as $quotation)
                            <tr>
                                <td>
                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-primary font-weight-bold">
                                        {{ $quotation->quotation_number }}
                                    </a>
                                </td>
                                <td>{{ $quotation->client->name }}</td>
                                <td>
                                    <span class="text-success font-weight-bold">
                                        {{ $settings->formatCurrency($quotation->total_amount) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        @if($quotation->accepted_date)
                                            {{ $quotation->accepted_date->format('M d, Y') }}
                                        @else
                                            -
                                        @endif
                                    </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Accepted Quotations</h5>
                    <p class="text-muted">Accepted quotations will appear here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Additional Analytics Row -->
<div class="row">
    <!-- Recent Invoices -->
    <div class="col-md-12">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice text-secondary"></i> Recent Invoices
                </h3>
                <div class="card-tools">
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> View All
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($recentInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentInvoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-primary font-weight-bold">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $invoice->client->name }}</td>
                                <td class="font-weight-bold">{{ $settings->formatCurrency($invoice->total_amount) }}</td>
                                <td>
                                    @switch($invoice->status)
                                        @case('draft')
                                            <span class="badge badge-secondary">Draft</span>
                                            @break
                                        @case('sent')
                                            <span class="badge badge-info">Sent</span>
                                            @break
                                        @case('viewed')
                                            <span class="badge badge-primary">Viewed</span>
                                            @break
                                        @case('paid')
                                            <span class="badge badge-success">Paid</span>
                                            @break
                                        @default
                                            <span class="badge badge-light">{{ ucfirst($invoice->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @switch($invoice->payment_status)
                                        @case('paid')
                                            <span class="badge badge-success">Paid</span>
                                            @break
                                        @case('partial')
                                            <span class="badge badge-warning">Partial</span>
                                            @break
                                        @case('unpaid')
                                            <span class="badge badge-danger">Unpaid</span>
                                            @break
                                        @default
                                            <span class="badge badge-light">{{ ucfirst($invoice->payment_status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <small class="text-muted">
                                        @if($invoice->invoice_date)
                                            {{ $invoice->invoice_date->format('M d, Y') }}
                                        @else
                                            -
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Invoices Yet</h4>
                    <p class="text-muted mb-4">Create your first invoice to get started!</p>
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Create Invoice
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Enhanced Card Styling */
.small-box {
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,.1), 0 1px 3px rgba(0,0,0,.08);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.small-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,.15), 0 4px 10px rgba(0,0,0,.12);
}

.small-box .inner {
    padding: 20px;
}

.small-box .inner h3 {
    font-weight: 700;
    font-size: 2.2rem;
    margin-bottom: 0;
}

.small-box .inner p {
    font-weight: 500;
    margin-bottom: 0;
    opacity: 0.9;
}

.small-box .icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 4rem;
    opacity: 0.3;
    transition: all 0.3s ease;
}

.small-box:hover .icon {
    opacity: 0.5;
    transform: scale(1.1);
}

.small-box-footer {
    background: rgba(0,0,0,0.1);
    color: rgba(255,255,255,0.8) !important;
    display: block;
    padding: 3px 0;
    text-align: center;
    text-decoration: none;
    z-index: 10;
    position: relative;
    transition: all 0.3s ease;
}

.small-box-footer:hover {
    color: #fff !important;
    background: rgba(0,0,0,0.2);
}

/* Custom Color Variations */
.bg-orange {
    background-color: #fd7e14 !important;
    color: #fff !important;
}

.bg-orange .small-box-footer {
    background: rgba(0,0,0,0.1);
}

/* Card Enhancements */
.card {
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,.04), 0 8px 16px rgba(0,0,0,.06);
    border: none;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,.08), 0 12px 24px rgba(0,0,0,.1);
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.card-outline.card-success {
    border-top: 3px solid #28a745;
}

.card-outline.card-info {
    border-top: 3px solid #17a2b8;
}

.card-outline.card-warning {
    border-top: 3px solid #ffc107;
}

.card-outline.card-secondary {
    border-top: 3px solid #6c757d;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid rgba(0,0,0,.05);
    border-radius: 12px 12px 0 0 !important;
    padding: 1rem 1.25rem;
}

.card-title {
    font-weight: 600;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Table Enhancements */
.table {
    margin-bottom: 0;
}

.table thead th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    background: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,.08);
    transition: all 0.2s ease;
}

.table-danger {
    background-color: rgba(220,53,69,.08) !important;
}

.table-danger:hover {
    background-color: rgba(220,53,69,.15) !important;
}

/* Badge Enhancements */
.badge {
    font-weight: 500;
    padding: 0.4em 0.8em;
    border-radius: 8px;
}

.badge-sm {
    font-size: 0.65em;
    padding: 0.25em 0.5em;
}

/* Button Enhancements */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-group-sm .btn {
    border-radius: 6px;
}

/* Animation Classes */
.fade-in {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-in-left {
    animation: slideInLeft 0.5s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.slide-in-right {
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive Enhancements */
@media (max-width: 768px) {
    .small-box .inner h3 {
        font-size: 1.8rem;
    }
    
    .small-box .icon {
        font-size: 3rem;
        top: 15px;
        right: 15px;
    }
    
    .card-header {
        padding: 0.75rem 1rem;
    }
}

/* Loading Animation */
.chart-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    color: #6c757d;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Document Ready Function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initializeAnimations();
    
    // Initialize charts
    initializeCharts();
    
    // Add interactive features
    addInteractiveFeatures();
});

// Animation Functions
function initializeAnimations() {
    // Animate statistics cards on load
    const smallBoxes = document.querySelectorAll('.small-box');
    smallBoxes.forEach((box, index) => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            box.style.transition = 'all 0.15s cubic-bezier(0.4, 0, 0.2, 1)';
            box.style.opacity = '1';
            box.style.transform = 'translateY(0)';
        }, index * 20);
    });
    
    // Animate cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.2s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (index * 30));
    });
}

// Chart Initialization
function initializeCharts() {
    // Revenue Trend Chart
    initializeRevenueChart();
    
    // Payment Status Chart
    initializePaymentChart();
}

// Revenue Chart
function initializeRevenueChart() {
    const revenueCtx = document.getElementById('revenueChart');
    if (!revenueCtx) return;
    
    const revenueData = @json($monthlyRevenue);
    const monthLabels = @json($monthLabels);
    
    // Create gradient
    const gradient = revenueCtx.getContext('2d').createLinearGradient(0, 0, 0, 350);
    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.3)');
    gradient.addColorStop(1, 'rgba(54, 162, 235, 0.05)');
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Monthly Revenue',
                data: revenueData,
                backgroundColor: gradient,
                borderColor: '#007bff',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#fff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#0056b3',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#007bff',
                    borderWidth: 2,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return 'Revenue for ' + context[0].label;
                        },
                        label: function(context) {
                            return '{{ $settings->currency_symbol }} ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        padding: 10,
                        callback: function(value) {
                            return '{{ $settings->currency_symbol }} ' + value.toLocaleString();
                        },
                        color: '#6c757d',
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        padding: 10,
                        color: '#6c757d',
                        font: {
                            size: 11
                        }
                    }
                }
            },
            animation: {
                duration: 600,
                easing: 'easeOutQuart'
            }
        }
    });
}

// Payment Status Chart
function initializePaymentChart() {
    const paymentCtx = document.getElementById('paymentChart');
    if (!paymentCtx) return;
    
    const paymentData = @json(array_values($paymentStatus));
    const paymentLabels = ['Paid', 'Unpaid', 'Partial'];
    
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                data: paymentData,
                backgroundColor: [
                    '#28a745',  // Green for paid
                    '#dc3545',  // Red for unpaid
                    '#ffc107'   // Yellow for partial
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 25,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        strokeStyle: data.datasets[0].backgroundColor[i],
                                        lineWidth: 0,
                                        pointStyle: 'circle',
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#6c757d',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${context.parsed} invoices (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 600,
                easing: 'easeOutQuart'
            }
        }
    });
}

// Interactive Features
function addInteractiveFeatures() {
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'all 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Add number counting animation for statistics
    const statNumbers = document.querySelectorAll('.small-box .inner h3');
    statNumbers.forEach((stat, index) => {
        const finalValue = stat.textContent;
        const numericValue = parseFloat(finalValue.replace(/[^0-9.-]+/g, ''));
        
        if (!isNaN(numericValue) && numericValue > 0) {
            stat.textContent = '0';
            
            setTimeout(() => {
                animateValue(stat, 0, numericValue, 300, finalValue.includes('%') || finalValue.includes('UGX') || finalValue.includes('$'));
            }, index * 30 + 150);
        }
    });
}

// Number Animation Function
function animateValue(element, start, end, duration, hasSymbol = false) {
    const range = end - start;
    const steps = Math.min(Math.abs(range), 50); // Limit steps for performance
    const increment = range / steps;
    const stepTime = duration / steps;
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        
        // Check if we've reached or passed the end
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        
        if (hasSymbol) {
            const originalText = element.textContent;
            const symbol = originalText.match(/[^0-9.,]/g)?.join('') || '';
            element.textContent = symbol + Math.floor(current).toLocaleString();
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, stepTime);
}

// Refresh Dashboard Data (for future real-time updates)
function refreshDashboard() {
    // This can be used for real-time updates via AJAX
    console.log('Dashboard refresh functionality ready');
}

// Error Handling for Charts
window.addEventListener('error', function(e) {
    if (e.message.includes('Chart')) {
        console.warn('Chart error detected, attempting recovery...');
        setTimeout(() => {
            initializeCharts();
        }, 1000);
    }
});

// Resize Handler for Charts
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        Chart.helpers.each(Chart.instances, (instance) => {
            instance.resize();
        });
    }, 300);
});
</script>
@stop
