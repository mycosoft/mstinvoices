@extends('adminlte::page')

@section('title', 'Daily POS Summary')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Daily POS Summary - {{ now()->format('Y-m-d') }}</h1>
        <a href="{{ route('pos.terminal') }}" class="btn btn-success btn-sm"><i class="fas fa-shopping-cart"></i> Open Terminal</a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content"><span class="info-box-text">Total Sales</span><span class="info-box-number">{{ $summary['total_sales'] }}</span></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
            <div class="info-box-content"><span class="info-box-text">Revenue</span><span class="info-box-number">{{ number_format($summary['total_revenue'], 0) }}</span></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-percent"></i></span>
            <div class="info-box-content"><span class="info-box-text">Tax Collected</span><span class="info-box-number">{{ number_format($summary['total_tax'], 0) }}</span></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-credit-card"></i></span>
            <div class="info-box-content"><span class="info-box-text">Collected</span><span class="info-box-number">{{ number_format($summary['total_paid'], 0) }}</span></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Payment Breakdown</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    @foreach($summary['payment_breakdown'] as $method => $total)
                    <tr><td>{{ ucfirst(str_replace('_', ' ', $method)) }}</td><td class="text-right">{{ number_format($total, 0) }}</td></tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>This Week</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Date</th><th class="text-right">Sales</th><th class="text-right">Count</th></tr></thead>
                    <tbody>
                        @forelse($weekSales as $day)
                        <tr>
                            <td>{{ $day->date }}</td>
                            <td class="text-right">{{ number_format($day->total, 0) }}</td>
                            <td class="text-right">{{ $day->count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-3">No sales this week</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Today's Recent Sales</strong></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>POS #</th><th>Time</th><th>Customer</th><th class="text-right">Total</th><th>Method</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($recentSales as $sale)
                <tr>
                    <td>{{ $sale->pos_number }}</td>
                    <td>{{ $sale->created_at->format('H:i') }}</td>
                    <td>{{ $sale->client?->name ?? 'Walk-in' }}</td>
                    <td class="text-right">{{ number_format($sale->total_amount, 0) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                    <td><a href="{{ route('pos.show', $sale) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-3">No sales today</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
