<!-- Revenue Report Summary -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-dollar-sign"></i> Revenue Summary
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['total_revenue'], 0) }}</span>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                {{ number_format($data['summary']['invoice_count']) }} invoices
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Paid Revenue</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['paid_revenue'], 0) }}</span>
                            @php
                                $paidPercentage = $data['summary']['total_revenue'] > 0 ? ($data['summary']['paid_revenue'] / $data['summary']['total_revenue']) * 100 : 0;
                            @endphp
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: {{ $paidPercentage }}%"></div>
                            </div>
                            <span class="progress-description">
                                {{ number_format($paidPercentage, 1) }}% of total revenue
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Revenue</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['pending_revenue'], 0) }}</span>
                            @php
                                $pendingPercentage = $data['summary']['total_revenue'] > 0 ? ($data['summary']['pending_revenue'] / $data['summary']['total_revenue']) * 100 : 0;
                            @endphp
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: {{ $pendingPercentage }}%"></div>
                            </div>
                            <span class="progress-description">
                                {{ number_format($pendingPercentage, 1) }}% outstanding
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-file-invoice"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Average Invoice</span>
                            <span class="info-box-number">UGX {{ number_format($data['summary']['average_invoice_value'], 0) }}</span>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">
                                From {{ number_format($data['summary']['invoice_count']) }} invoices
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Row -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="description-block border-right">
                        <span class="description-percentage text-{{ $paidPercentage >= 80 ? 'success' : ($paidPercentage >= 50 ? 'warning' : 'danger') }}">
                            <i class="fas fa-{{ $paidPercentage >= 80 ? 'arrow-up' : ($paidPercentage >= 50 ? 'minus' : 'arrow-down') }}"></i>
                            {{ number_format($paidPercentage, 1) }}%
                        </span>
                        <h5 class="description-header">UGX {{ number_format($data['summary']['paid_revenue'], 0) }}</h5>
                        <span class="description-text">COLLECTION RATE</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="description-block border-right">
                        <span class="description-percentage text-primary">
                            <i class="fas fa-calculator"></i>
                        </span>
                        <h5 class="description-header">UGX {{ number_format($data['summary']['average_invoice_value'], 0) }}</h5>
                        <span class="description-text">AVG INVOICE VALUE</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="description-block">
                        <span class="description-percentage text-{{ $data['summary']['pending_revenue'] == 0 ? 'success' : 'warning' }}">
                            <i class="fas fa-{{ $data['summary']['pending_revenue'] == 0 ? 'check-circle' : 'exclamation-triangle' }}"></i>
                        </span>
                        <h5 class="description-header">UGX {{ number_format($data['summary']['pending_revenue'], 0) }}</h5>
                        <span class="description-text">OUTSTANDING</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Charts Section -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> Revenue Analytics
            </h3>
            <div class="card-tools">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="switchChart('line')">
                        <i class="fas fa-chart-line"></i> Line
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="switchChart('bar')">
                        <i class="fas fa-chart-bar"></i> Bar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="switchChart('area')">
                        <i class="fas fa-chart-area"></i> Area
                    </button>
                </div>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Revenue Donut Chart -->
                    <div class="chart-container" style="position: relative; height: 200px; margin-bottom: 20px;">
                        <canvas id="revenueDonutChart"></canvas>
                    </div>
                    <!-- Payment Status Chart -->
                    <div class="chart-container" style="position: relative; height: 180px;">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Performance Table -->
@if(count($data['monthly_data']) > 0)
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i> Monthly Performance Details
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" onclick="exportTableToCSV()" title="Export to CSV">
                    <i class="fas fa-download"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="monthlyRevenueTable">
                    <thead class="bg-light">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Month</th>
                            <th>Year</th>
                            <th class="text-center"><i class="fas fa-file-invoice"></i> Invoices</th>
                            <th class="text-right"><i class="fas fa-dollar-sign"></i> Total Revenue</th>
                            <th class="text-right"><i class="fas fa-check"></i> Paid Revenue</th>
                            <th class="text-right"><i class="fas fa-clock"></i> Pending</th>
                            <th class="text-right"><i class="fas fa-calculator"></i> Avg Invoice</th>
                            <th class="text-center"><i class="fas fa-percentage"></i> Payment Rate</th>
                            <th class="text-center"><i class="fas fa-chart-line"></i> Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $previousRevenue = 0; @endphp
                        @foreach($data['monthly_data'] as $index => $monthData)
                        <tr>
                            <td>
                                <strong>{{ DateTime::createFromFormat('!m', $monthData->month)->format('F') }}</strong>
                            </td>
                            <td>{{ $monthData->year }}</td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ number_format($monthData->invoice_count) }}</span>
                            </td>
                            <td class="text-right">
                                <strong>UGX {{ number_format($monthData->total_revenue, 0) }}</strong>
                            </td>
                            <td class="text-right">
                                UGX {{ number_format($monthData->paid_revenue, 0) }}
                            </td>
                            <td class="text-right">
                                @php $pending = $monthData->total_revenue - $monthData->paid_revenue; @endphp
                                <span class="text-{{ $pending > 0 ? 'warning' : 'success' }}">
                                    UGX {{ number_format($pending, 0) }}
                                </span>
                            </td>
                            <td class="text-right">
                                UGX {{ number_format($monthData->invoice_count > 0 ? $monthData->total_revenue / $monthData->invoice_count : 0, 0) }}
                            </td>
                            <td class="text-center">
                                @php $rate = $monthData->total_revenue > 0 ? ($monthData->paid_revenue / $monthData->total_revenue) * 100 : 0; @endphp
                                <span class="badge badge-{{ $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}">
                                    {{ number_format($rate, 1) }}%
                                </span>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $rate }}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($index > 0 && $previousRevenue > 0)
                                    @php $growth = (($monthData->total_revenue - $previousRevenue) / $previousRevenue) * 100; @endphp
                                    <span class="badge badge-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $growth >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                        {{ number_format(abs($growth), 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                                @php $previousRevenue = $monthData->total_revenue; @endphp
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="font-weight-bold">
                            <td colspan="2">Total</td>
                            <td class="text-center">
                                <span class="badge badge-dark">{{ number_format($data['monthly_data']->sum('invoice_count')) }}</span>
                            </td>
                            <td class="text-right">
                                <strong>UGX {{ number_format($data['monthly_data']->sum('total_revenue'), 0) }}</strong>
                            </td>
                            <td class="text-right">
                                <strong>UGX {{ number_format($data['monthly_data']->sum('paid_revenue'), 0) }}</strong>
                            </td>
                            <td class="text-right">
                                @php
                                    $totalPending = $data['monthly_data']->sum('total_revenue') - $data['monthly_data']->sum('paid_revenue');
                                @endphp
                                <strong class="text-{{ $totalPending > 0 ? 'warning' : 'success' }}">
                                    UGX {{ number_format($totalPending, 0) }}
                                </strong>
                            </td>
                            <td class="text-right">
                                @php
                                    $totalInvoices = $data['monthly_data']->sum('invoice_count');
                                    $avgInvoice = $totalInvoices > 0 ? $data['monthly_data']->sum('total_revenue') / $totalInvoices : 0;
                                @endphp
                                <strong>UGX {{ number_format($avgInvoice, 0) }}</strong>
                            </td>
                            <td class="text-center">
                                @php
                                    $overallRate = $data['monthly_data']->sum('total_revenue') > 0 ? 
                                        ($data['monthly_data']->sum('paid_revenue') / $data['monthly_data']->sum('total_revenue')) * 100 : 0;
                                @endphp
                                <span class="badge badge-{{ $overallRate >= 80 ? 'success' : ($overallRate >= 50 ? 'warning' : 'danger') }}">
                                    {{ number_format($overallRate, 1) }}%
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $firstMonth = $data['monthly_data']->first();
                                    $lastMonth = $data['monthly_data']->last();
                                    $overallGrowth = $firstMonth && $lastMonth && $firstMonth->total_revenue > 0 ? 
                                        (($lastMonth->total_revenue - $firstMonth->total_revenue) / $firstMonth->total_revenue) * 100 : 0;
                                @endphp
                                <span class="badge badge-{{ $overallGrowth >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $overallGrowth >= 0 ? 'trending-up' : 'trending-down' }}"></i>
                                    {{ number_format(abs($overallGrowth), 1) }}%
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Key Insights -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lightbulb"></i> Key Insights
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Performance Indicators</h5>
                    <ul class="list-unstyled">
                        @php
                            $paymentRate = $data['summary']['total_revenue'] > 0 ? ($data['summary']['paid_revenue'] / $data['summary']['total_revenue']) * 100 : 0;
                        @endphp
                        <li>
                            <i class="fas fa-circle text-{{ $paymentRate >= 80 ? 'success' : ($paymentRate >= 50 ? 'warning' : 'danger') }}"></i>
                            Payment collection rate: <strong>{{ number_format($paymentRate, 1) }}%</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-info"></i>
                            Average invoice value: <strong>UGX {{ number_format($data['summary']['average_invoice_value'], 0) }}</strong>
                        </li>
                        <li>
                            <i class="fas fa-circle text-primary"></i>
                            Outstanding amount: <strong>UGX {{ number_format($data['summary']['pending_revenue'], 0) }}</strong>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Recommendations</h5>
                    <ul class="list-unstyled">
                        @if($paymentRate < 50)
                            <li><i class="fas fa-exclamation-triangle text-danger"></i> Consider implementing stricter payment terms</li>
                        @endif
                        @if($data['summary']['pending_revenue'] > 0)
                            <li><i class="fas fa-bell text-warning"></i> Follow up on outstanding invoices</li>
                        @endif
                        @if($data['summary']['average_invoice_value'] < 100000)
                            <li><i class="fas fa-arrow-up text-info"></i> Consider strategies to increase average invoice value</li>
                        @endif
                        @if($paymentRate >= 80)
                            <li><i class="fas fa-thumbs-up text-success"></i> Excellent payment collection rate!</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>