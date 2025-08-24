@extends('adminlte::page')

@section('title', 'Product Performance Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-box"></i> Product Performance Report</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-download"></i> Export
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('reports.quick.export.pdf', ['type' => 'product_performance', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank">
                    <i class="fas fa-file-pdf text-danger"></i> Export as PDF
                </a>
                <a class="dropdown-item" href="{{ route('reports.quick.export.excel', ['type' => 'product_performance', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    <i class="fas fa-file-excel text-success"></i> Export as Excel
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Date Range Filter -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Date Range Filter</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.quick.product-performance') }}" class="row">
                    <div class="col-md-4">
                        <label for="date_from">From Date:</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to">To Date:</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Update Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary elevation-1">
                <i class="fas fa-box"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Items Sold</span>
                <span class="info-box-number">{{ number_format($data['summary']['total_items_sold']) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-dollar-sign"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Revenue</span>
                <span class="info-box-number">UGX {{ number_format($data['summary']['total_revenue'], 0) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-cubes"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Unique Products</span>
                <span class="info-box-number">{{ number_format($data['summary']['unique_products']) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-calculator"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Avg Revenue/Product</span>
                <span class="info-box-number">UGX {{ number_format($data['summary']['avg_revenue_per_product'], 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Product Performance Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i> Product Performance Details
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Product/Service</th>
                                <th class="text-center">Quantity Sold</th>
                                <th class="text-right">Total Revenue</th>
                                <th class="text-center">Invoice Count</th>
                                <th class="text-right">Avg Unit Price</th>
                                <th class="text-center">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['product_data'] as $product)
                            <tr>
                                <td>
                                    <strong>{{ $product->description }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ number_format($product->total_quantity) }}</span>
                                </td>
                                <td class="text-right">
                                    <strong>UGX {{ number_format($product->total_revenue, 0) }}</strong>
                                </td>
                                <td class="text-center">
                                    {{ number_format($product->invoice_count) }}
                                </td>
                                <td class="text-right">
                                    UGX {{ number_format($product->avg_unit_price, 0) }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $percentage = $data['summary']['total_revenue'] > 0 ? ($product->total_revenue / $data['summary']['total_revenue']) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $percentage >= 20 ? 'success' : ($percentage >= 10 ? 'warning' : 'info') }}" 
                                             style="width: {{ min($percentage, 100) }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.info-box {
    margin-bottom: 20px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}
</style>
@stop

@section('js')
<script>
// Auto-refresh when dates change
document.getElementById('date_from').addEventListener('change', function() {
    if (this.form.date_to.value) {
        this.form.submit();
    }
});

document.getElementById('date_to').addEventListener('change', function() {
    if (this.form.date_from.value) {
        this.form.submit();
    }
});
</script>
@stop