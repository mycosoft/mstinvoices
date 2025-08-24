<!-- Tax Summary Report -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-percent"></i> Tax Summary Overview
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-percent"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Tax Collected</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['total_tax_collected'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Taxable Revenue</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['taxable_revenue'], 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Effective Tax Rate</span>
                            <span class="info-box-number">{{ number_format($data['summary']['effective_tax_rate'], 2) }}%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-receipt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Taxable Invoices</span>
                            <span class="info-box-number">{{ number_format($data['summary']['taxable_invoices']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-money-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tax Paid</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['tax_paid'], 0) }}</span>
                            <div class="info-box-more">{{ number_format($data['summary']['tax_payment_rate'], 1) }}% collected</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tax Outstanding</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['tax_outstanding'], 0) }}</span>
                            <div class="info-box-more">{{ number_format(100 - $data['summary']['tax_payment_rate'], 1) }}% pending</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-dark"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Average Tax/Invoice</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['average_tax_per_invoice'], 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($report->format === 'chart')
<!-- Tax Rate Distribution -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-pie"></i> Tax Rate Distribution
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="taxRateChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Tax Collection -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Tax Collection Status
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="taxCollectionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tax Trend Over Time -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> Monthly Tax Collection Trend
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="taxTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@if($report->format === 'table' || $report->format === 'summary')
<!-- Tax Breakdown by Rate -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-percentage"></i> Tax Breakdown by Rate
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tax Rate</th>
                            <th class="text-right">Invoices</th>
                            <th class="text-right">Taxable Amount</th>
                            <th class="text-right">Tax Amount</th>
                            <th class="text-right">Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['tax_rates'] as $rate => $rateData)
                        <tr>
                            <td>
                                <span class="badge badge-{{ $rate == 0 ? 'secondary' : ($rate <= 10 ? 'info' : ($rate <= 20 ? 'warning' : 'danger')) }}">
                                    {{ number_format($rate, 1) }}%
                                </span>
                            </td>
                            <td class="text-right">{{ number_format($rateData['invoice_count']) }}</td>
                            <td class="text-right">UGX {{ number_format($rateData['taxable_amount'], 0) }}</td>
                            <td class="text-right">UGX {{ number_format($rateData['tax_amount'], 0) }}</td>
                            <td class="text-right">
                                UGX {{ number_format($rateData['tax_collected'], 0) }}
                                <br><small class="text-muted">
                                    ({{ number_format($rateData['collection_rate'], 1) }}%)
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No tax data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($data['tax_rates']) > 0)
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td>Total</td>
                            <td class="text-right">{{ number_format(collect($data['tax_rates'])->sum('invoice_count')) }}</td>
                            <td class="text-right">UGX {{ number_format(collect($data['tax_rates'])->sum('taxable_amount'), 0) }}</td>
                            <td class="text-right">UGX {{ number_format(collect($data['tax_rates'])->sum('tax_amount'), 0) }}</td>
                            <td class="text-right">UGX {{ number_format(collect($data['tax_rates'])->sum('tax_collected'), 0) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Tax Summary -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar"></i> Monthly Tax Summary
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-right">Tax Due</th>
                            <th class="text-right">Tax Collected</th>
                            <th class="text-right">Outstanding</th>
                            <th class="text-right">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['monthly_tax'] as $month)
                        <tr>
                            <td>
                                <strong>{{ DateTime::createFromFormat('!m', $month->month)->format('M') }} {{ $month->year }}</strong>
                            </td>
                            <td class="text-right">UGX {{ number_format($month->tax_due, 0) }}</td>
                            <td class="text-right">UGX {{ number_format($month->tax_collected, 0) }}</td>
                            <td class="text-right">UGX {{ number_format($month->tax_due - $month->tax_collected, 0) }}</td>
                            <td class="text-right">
                                @php
                                    $monthlyRate = $month->tax_due > 0 ? ($month->tax_collected / $month->tax_due) * 100 : 0;
                                @endphp
                                <span class="badge badge-{{ $monthlyRate >= 90 ? 'success' : ($monthlyRate >= 70 ? 'warning' : 'danger') }}">
                                    {{ number_format($monthlyRate, 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No monthly tax data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tax Compliance Status -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check"></i> Tax Compliance Analysis
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-{{ $data['summary']['tax_payment_rate'] >= 90 ? 'success' : ($data['summary']['tax_payment_rate'] >= 70 ? 'warning' : 'danger') }}">
                        <div class="inner">
                            <h3>{{ number_format($data['summary']['tax_payment_rate'], 1) }}%</h3>
                            <p>Collection Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>UGX {{ number_format($data['summary']['tax_outstanding']/1000, 0) }}K</h3>
                            <p>Outstanding Tax</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($data['summary']['overdue_tax_invoices']) }}</h3>
                            <p>Overdue Invoices</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ number_format($data['summary']['effective_tax_rate'], 1) }}%</h3>
                            <p>Effective Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Tax Contributing Clients -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i> Top Tax Contributing Clients
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th class="text-right">Taxable Revenue</th>
                            <th class="text-right">Tax Due</th>
                            <th class="text-right">Tax Paid</th>
                            <th class="text-right">Outstanding</th>
                            <th class="text-right">Payment Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['top_tax_clients'] as $client)
                        <tr>
                            <td>
                                <strong>{{ $client->client_name }}</strong>
                                @if($client->client_company)
                                    <br><small class="text-muted">{{ $client->client_company }}</small>
                                @endif
                            </td>
                            <td class="text-right">UGX {{ number_format($client->taxable_revenue, 0) }}</td>
                            <td class="text-right">UGX {{ number_format($client->tax_due, 0) }}</td>
                            <td class="text-right">UGX {{ number_format($client->tax_paid, 0) }}</td>
                            <td class="text-right">UGX {{ number_format($client->tax_due - $client->tax_paid, 0) }}</td>
                            <td class="text-right">
                                @php
                                    $clientRate = $client->tax_due > 0 ? ($client->tax_paid / $client->tax_due) * 100 : 0;
                                @endphp
                                <span class="badge badge-{{ $clientRate >= 90 ? 'success' : ($clientRate >= 70 ? 'warning' : 'danger') }}">
                                    {{ number_format($clientRate, 1) }}%
                                </span>
                            </td>
                            <td>
                                @if($client->tax_due - $client->tax_paid <= 0)
                                    <span class="badge badge-success">Compliant</span>
                                @elseif($clientRate >= 70)
                                    <span class="badge badge-warning">Partial</span>
                                @else
                                    <span class="badge badge-danger">Outstanding</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No client tax data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tax Insights and Recommendations -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lightbulb"></i> Tax Analysis & Compliance Insights
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Tax Performance Indicators</h5>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['tax_payment_rate'] >= 90 ? 'success' : ($data['summary']['tax_payment_rate'] >= 70 ? 'warning' : 'danger') }}"></i>
                            Tax collection efficiency: <strong>{{ number_format($data['summary']['tax_payment_rate'], 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['effective_tax_rate'] >= 15 ? 'info' : 'secondary' }}"></i>
                            Effective tax rate: <strong>{{ number_format($data['summary']['effective_tax_rate'], 2) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-{{ $data['summary']['tax_outstanding'] == 0 ? 'success' : ($data['summary']['tax_outstanding'] <= 1000000 ? 'warning' : 'danger') }}"></i>
                            Outstanding tax liability: <strong>UGX {{ number_format($data['summary']['tax_outstanding'], 0) }}</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-primary"></i>
                            Monthly tax collection: <strong>UGX {{ number_format($data['summary']['total_tax_collected'] / max(1, count($data['monthly_tax'])), 0) }}</strong>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Compliance Recommendations</h5>
                    <ul class="list-unstyled">
                        @if($data['summary']['tax_payment_rate'] < 80)
                            <li><i class="fas fa-exclamation-triangle text-danger"></i> Improve tax collection procedures</li>
                        @endif
                        @if($data['summary']['tax_outstanding'] > 2000000)
                            <li><i class="fas fa-money-check text-warning"></i> Prioritize collection of outstanding tax amounts</li>
                        @endif
                        @if($data['summary']['overdue_tax_invoices'] > 10)
                            <li><i class="fas fa-bell text-danger"></i> Implement automated tax collection reminders</li>
                        @endif
                        @if($data['summary']['effective_tax_rate'] < 10)
                            <li><i class="fas fa-search text-info"></i> Review tax rate application and compliance</li>
                        @endif
                        @if($data['summary']['tax_payment_rate'] >= 90 && $data['summary']['tax_outstanding'] <= 500000)
                            <li><i class="fas fa-trophy text-success"></i> Excellent tax compliance performance!</li>
                        @endif
                        <li><i class="fas fa-file-alt text-primary"></i> Maintain detailed records for tax reporting</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
@parent
<script>
$(document).ready(function() {
    @if($report->format === 'chart')
        initializeTaxRateChart();
        initializeTaxCollectionChart();
        initializeTaxTrendChart();
    @endif
});

function initializeTaxRateChart() {
    const ctx = document.getElementById('taxRateChart');
    if (!ctx) return;
    
    const taxRates = @json($data['tax_rates']);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(taxRates).map(rate => rate + '%'),
            datasets: [{
                data: Object.values(taxRates).map(item => item.tax_amount),
                backgroundColor: [
                    'rgba(108, 117, 125, 0.8)',  // secondary - 0%
                    'rgba(23, 162, 184, 0.8)',   // info - low rates
                    'rgba(255, 193, 7, 0.8)',    // warning - medium rates
                    'rgba(220, 53, 69, 0.8)',    // danger - high rates
                    'rgba(40, 167, 69, 0.8)'     // success - standard rates
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const rate = Object.keys(taxRates)[context.dataIndex];
                            const data = taxRates[rate];
                            return rate + '%: UGX ' + data.tax_amount.toLocaleString() + 
                                   ' (' + data.invoice_count + ' invoices)';
                        }
                    }
                }
            }
        }
    });
}

function initializeTaxCollectionChart() {
    const ctx = document.getElementById('taxCollectionChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Tax Due', 'Tax Collected', 'Outstanding'],
            datasets: [{
                label: 'Amount (UGX)',
                data: [
                    {{ $data['summary']['total_tax_collected'] + $data['summary']['tax_outstanding'] }},
                    {{ $data['summary']['total_tax_collected'] }},
                    {{ $data['summary']['tax_outstanding'] }}
                ],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.8)',    // primary - due
                    'rgba(40, 167, 69, 0.8)',    // success - collected
                    'rgba(220, 53, 69, 0.8)'     // danger - outstanding
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': UGX ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function initializeTaxTrendChart() {
    const ctx = document.getElementById('taxTrendChart');
    if (!ctx) return;
    
    const monthlyData = @json($data['monthly_tax']);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => `${item.month}/${item.year}`),
            datasets: [{
                label: 'Tax Due',
                data: monthlyData.map(item => item.tax_due),
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4
            }, {
                label: 'Tax Collected',
                data: monthlyData.map(item => item.tax_collected),
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': UGX ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection