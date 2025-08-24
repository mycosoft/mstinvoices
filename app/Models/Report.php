<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Report extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
        'status',
        'filters',
        'columns',
        'format',
        'chart_type',
        'date_from',
        'date_to',
        'date_range_type',
        'is_scheduled',
        'schedule_frequency',
        'schedule_day',
        'schedule_time',
        'last_generated_at',
        'next_generation_at',
        'last_result',
        'generation_count',
        'auto_email',
        'email_recipients',
        'export_formats',
        'report_file_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'email_recipients' => 'array',
        'export_formats' => 'array',
        'last_result' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
        'last_generated_at' => 'datetime',
        'next_generation_at' => 'datetime',
        'schedule_time' => 'datetime:H:i',
        'is_scheduled' => 'boolean',
        'auto_email' => 'boolean',
        'generation_count' => 'integer',
        'schedule_day' => 'integer',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active reports.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for scheduled reports.
     */
    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true);
    }

    /**
     * Scope for reports due for generation.
     */
    public function scopeDueForGeneration($query)
    {
        return $query->where('is_scheduled', true)
                    ->where('next_generation_at', '<=', now());
    }

    /**
     * Get available report types.
     */
    public static function getReportTypes()
    {
        return [
            'revenue' => 'Revenue Report',
            'client' => 'Client Performance Report',
            'monthly' => 'Monthly Summary Report',
            'yearly' => 'Yearly Summary Report',
            'invoice_summary' => 'Invoice Summary Report',
            'payment_summary' => 'Payment Summary Report',
            'tax_summary' => 'Tax Summary Report',
            'product_performance' => 'Product Performance Report',
            'overdue_invoices' => 'Overdue Invoices Report',
            'cash_flow' => 'Cash Flow Report',
            'profit_loss' => 'Profit & Loss Report',
            'top_clients' => 'Top Clients Report',
            'growth_analysis' => 'Growth Analysis Report',
            'payment_methods' => 'Payment Methods Report',
            'seasonal_trends' => 'Seasonal Trends Report',
            'custom' => 'Custom Report',
        ];
    }

    /**
     * Get available date range types.
     */
    public static function getDateRangeTypes()
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'last_quarter' => 'Last Quarter',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range',
        ];
    }

    /**
     * Get date range based on type.
     */
    public function getDateRange()
    {
        $now = Carbon::now();
        
        switch ($this->date_range_type) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'yesterday':
                return [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()];
            case 'this_week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'last_week':
                return [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()];
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'last_month':
                return [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()];
            case 'this_quarter':
                return [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()];
            case 'last_quarter':
                return [$now->copy()->subQuarter()->startOfQuarter(), $now->copy()->subQuarter()->endOfQuarter()];
            case 'this_year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            case 'last_year':
                return [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()];
            case 'custom':
            default:
                return [$this->date_from, $this->date_to];
        }
    }

    /**
     * Generate report data based on type.
     */
    public function generateData()
    {
        [$dateFrom, $dateTo] = $this->getDateRange();
        
        switch ($this->type) {
            case 'revenue':
                return $this->generateRevenueReport($dateFrom, $dateTo);
            case 'client':
                return $this->generateClientReport($dateFrom, $dateTo);
            case 'monthly':
                return $this->generateMonthlyReport($dateFrom, $dateTo);
            case 'yearly':
                return $this->generateYearlyReport($dateFrom, $dateTo);
            case 'invoice_summary':
                return $this->generateInvoiceSummaryReport($dateFrom, $dateTo);
            case 'payment_summary':
                return $this->generatePaymentSummaryReport($dateFrom, $dateTo);
            case 'tax_summary':
                return $this->generateTaxSummaryReport($dateFrom, $dateTo);
            case 'product_performance':
                return $this->generateProductPerformanceReport($dateFrom, $dateTo);
            case 'overdue_invoices':
                return $this->generateOverdueInvoicesReport($dateFrom, $dateTo);
            case 'cash_flow':
                return $this->generateCashFlowReport($dateFrom, $dateTo);
            case 'profit_loss':
                return $this->generateProfitLossReport($dateFrom, $dateTo);
            case 'top_clients':
                return $this->generateTopClientsReport($dateFrom, $dateTo);
            case 'growth_analysis':
                return $this->generateGrowthAnalysisReport($dateFrom, $dateTo);
            case 'payment_methods':
                return $this->generatePaymentMethodsReport($dateFrom, $dateTo);
            case 'seasonal_trends':
                return $this->generateSeasonalTrendsReport($dateFrom, $dateTo);
            default:
                return $this->generateCustomReport($dateFrom, $dateTo);
        }
    }

    /**
     * Generate revenue report.
     */
    private function generateRevenueReport($dateFrom, $dateTo)
    {
        $invoices = Invoice::where('user_id', $this->user_id)
                          ->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        
        $totalRevenue = $invoices->sum('total_amount');
        $paidRevenue = $invoices->where('payment_status', 'paid')->sum('total_amount');
        $pendingRevenue = $invoices->whereIn('payment_status', ['unpaid', 'partial'])->sum('balance_due');
        $invoiceCount = $invoices->count();
        
        // Monthly breakdown
        $monthlyData = $invoices->selectRaw('
                YEAR(invoice_date) as year,
                MONTH(invoice_date) as month,
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_revenue
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        return [
            'summary' => [
                'total_revenue' => $totalRevenue,
                'paid_revenue' => $paidRevenue,
                'pending_revenue' => $pendingRevenue,
                'invoice_count' => $invoiceCount,
                'average_invoice_value' => $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0,
            ],
            'monthly_data' => $monthlyData,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate client report.
     */
    private function generateClientReport($dateFrom, $dateTo)
    {
        $clientData = DB::table('clients')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('clients.user_id', $this->user_id)
            ->whereBetween('invoices.invoice_date', [$dateFrom, $dateTo])
            ->select(
                'clients.id',
                'clients.name',
                'clients.company_name',
                DB::raw('COUNT(invoices.id) as invoice_count'),
                DB::raw('SUM(invoices.total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN invoices.payment_status = "paid" THEN invoices.total_amount ELSE 0 END) as paid_revenue'),
                DB::raw('SUM(invoices.balance_due) as outstanding_amount')
            )
            ->groupBy('clients.id', 'clients.name', 'clients.company_name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        return [
            'client_data' => $clientData,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate monthly report.
     */
    private function generateMonthlyReport($dateFrom, $dateTo)
    {
        // Implementation for monthly report
        return $this->generateRevenueReport($dateFrom, $dateTo);
    }

    /**
     * Generate yearly report.
     */
    private function generateYearlyReport($dateFrom, $dateTo)
    {
        // Implementation for yearly report
        return $this->generateRevenueReport($dateFrom, $dateTo);
    }

    /**
     * Generate invoice summary report.
     */
    private function generateInvoiceSummaryReport($dateFrom, $dateTo)
    {
        $invoices = Invoice::where('user_id', $this->user_id)
                          ->whereBetween('invoice_date', [$dateFrom, $dateTo])
                          ->with('client')
                          ->get();
        
        $statusSummary = $invoices->groupBy('status')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('total_amount'),
            ];
        });
        
        return [
            'invoices' => $invoices,
            'status_summary' => $statusSummary,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate payment summary report.
     */
    private function generatePaymentSummaryReport($dateFrom, $dateTo)
    {
        // Implementation for payment summary
        return $this->generateRevenueReport($dateFrom, $dateTo);
    }

    /**
     * Generate tax summary report.
     */
    private function generateTaxSummaryReport($dateFrom, $dateTo)
    {
        $taxData = Invoice::where('user_id', $this->user_id)
                         ->whereBetween('invoice_date', [$dateFrom, $dateTo])
                         ->selectRaw('
                             SUM(tax_amount) as total_tax,
                             SUM(subtotal) as total_subtotal,
                             COUNT(*) as invoice_count
                         ')
                         ->first();
        
        return [
            'tax_summary' => $taxData,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate custom report.
     */
    private function generateCustomReport($dateFrom, $dateTo)
    {
        // Implementation for custom report based on filters
        return $this->generateRevenueReport($dateFrom, $dateTo);
    }

    /**
     * Update generation tracking.
     */
    public function updateGenerationTracking()
    {
        $this->increment('generation_count');
        $this->update([
            'last_generated_at' => now(),
            'next_generation_at' => $this->calculateNextGeneration(),
        ]);
    }

    /**
     * Calculate next generation time for scheduled reports.
     */
    private function calculateNextGeneration()
    {
        if (!$this->is_scheduled) {
            return null;
        }
        
        $now = Carbon::now();
        
        switch ($this->schedule_frequency) {
            case 'daily':
                return $now->addDay()->setTimeFromTimeString($this->schedule_time);
            case 'weekly':
                return $now->addWeek()->setTimeFromTimeString($this->schedule_time);
            case 'monthly':
                return $now->addMonth()->setTimeFromTimeString($this->schedule_time);
            case 'quarterly':
                return $now->addQuarter()->setTimeFromTimeString($this->schedule_time);
            case 'yearly':
                return $now->addYear()->setTimeFromTimeString($this->schedule_time);
            default:
                return null;
        }
    }

    /**
     * Generate product performance report.
     */
    private function generateProductPerformanceReport($dateFrom, $dateTo)
    {
        $itemData = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.user_id', $this->user_id)
            ->whereBetween('invoices.invoice_date', [$dateFrom, $dateTo])
            ->select(
                'invoice_items.description',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.quantity * invoice_items.unit_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT invoices.id) as invoice_count'),
                DB::raw('AVG(invoice_items.unit_price) as avg_unit_price')
            )
            ->groupBy('invoice_items.description')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $totalItems = $itemData->sum('total_quantity');
        $totalRevenue = $itemData->sum('total_revenue');

        return [
            'summary' => [
                'total_items_sold' => $totalItems,
                'total_revenue' => $totalRevenue,
                'unique_products' => $itemData->count(),
                'avg_revenue_per_product' => $itemData->count() > 0 ? $totalRevenue / $itemData->count() : 0,
            ],
            'product_data' => $itemData,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate overdue invoices report.
     */
    private function generateOverdueInvoicesReport($dateFrom, $dateTo)
    {
        $overdueInvoices = Invoice::where('user_id', $this->user_id)
            ->where('due_date', '<', now())
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->with('client')
            ->orderBy('due_date')
            ->get();

        $totalOverdue = $overdueInvoices->sum('balance_due');
        $averageDaysOverdue = $overdueInvoices->avg(function($invoice) {
            return now()->diffInDays($invoice->due_date);
        });

        // Age analysis
        $ageGroups = [
            '0-30' => $overdueInvoices->filter(fn($inv) => now()->diffInDays($inv->due_date) <= 30)->sum('balance_due'),
            '31-60' => $overdueInvoices->filter(fn($inv) => now()->diffInDays($inv->due_date) > 30 && now()->diffInDays($inv->due_date) <= 60)->sum('balance_due'),
            '61-90' => $overdueInvoices->filter(fn($inv) => now()->diffInDays($inv->due_date) > 60 && now()->diffInDays($inv->due_date) <= 90)->sum('balance_due'),
            '90+' => $overdueInvoices->filter(fn($inv) => now()->diffInDays($inv->due_date) > 90)->sum('balance_due'),
        ];

        return [
            'summary' => [
                'total_overdue_amount' => $totalOverdue,
                'overdue_count' => $overdueInvoices->count(),
                'average_days_overdue' => $averageDaysOverdue ?? 0,
                'oldest_overdue_days' => $overdueInvoices->max(fn($inv) => now()->diffInDays($inv->due_date)) ?? 0,
            ],
            'overdue_invoices' => $overdueInvoices,
            'age_groups' => $ageGroups,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate cash flow report.
     */
    private function generateCashFlowReport($dateFrom, $dateTo)
    {
        $monthlyFlow = DB::table('invoices')
            ->where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->selectRaw('
                YEAR(invoice_date) as year,
                MONTH(invoice_date) as month,
                SUM(total_amount) as invoiced_amount,
                SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as received_amount,
                SUM(balance_due) as pending_amount
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $totalInvoiced = $monthlyFlow->sum('invoiced_amount');
        $totalReceived = $monthlyFlow->sum('received_amount');
        $totalPending = $monthlyFlow->sum('pending_amount');

        return [
            'summary' => [
                'total_invoiced' => $totalInvoiced,
                'total_received' => $totalReceived,
                'total_pending' => $totalPending,
                'collection_rate' => $totalInvoiced > 0 ? ($totalReceived / $totalInvoiced) * 100 : 0,
            ],
            'monthly_flow' => $monthlyFlow,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate profit and loss report.
     */
    private function generateProfitLossReport($dateFrom, $dateTo)
    {
        $revenue = Invoice::where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $taxAmount = Invoice::where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->where('payment_status', 'paid')
            ->sum('tax_amount');

        $netRevenue = $revenue - $taxAmount;
        $grossProfit = $netRevenue; // Assuming no cost of goods for service business
        $netProfit = $grossProfit; // Assuming no other expenses tracked

        return [
            'summary' => [
                'gross_revenue' => $revenue,
                'tax_amount' => $taxAmount,
                'net_revenue' => $netRevenue,
                'gross_profit' => $grossProfit,
                'net_profit' => $netProfit,
                'profit_margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
            ],
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate top clients report.
     */
    private function generateTopClientsReport($dateFrom, $dateTo)
    {
        $topClients = DB::table('clients')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('clients.user_id', $this->user_id)
            ->whereBetween('invoices.invoice_date', [$dateFrom, $dateTo])
            ->select(
                'clients.id',
                'clients.name',
                'clients.company_name',
                'clients.email',
                DB::raw('COUNT(invoices.id) as invoice_count'),
                DB::raw('SUM(invoices.total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN invoices.payment_status = "paid" THEN invoices.total_amount ELSE 0 END) as paid_revenue'),
                DB::raw('AVG(invoices.total_amount) as avg_invoice_value'),
                DB::raw('SUM(invoices.balance_due) as outstanding_amount')
            )
            ->groupBy('clients.id', 'clients.name', 'clients.company_name', 'clients.email')
            ->orderBy('total_revenue', 'desc')
            ->limit(20)
            ->get();

        $totalRevenue = $topClients->sum('total_revenue');
        $totalClients = $topClients->count();

        return [
            'summary' => [
                'total_clients' => $totalClients,
                'total_revenue' => $totalRevenue,
                'avg_revenue_per_client' => $totalClients > 0 ? $totalRevenue / $totalClients : 0,
                'top_client_revenue' => $topClients->first()->total_revenue ?? 0,
            ],
            'top_clients' => $topClients,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate growth analysis report.
     */
    private function generateGrowthAnalysisReport($dateFrom, $dateTo)
    {
        // Get data for current period
        $currentData = Invoice::where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->selectRaw('
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_invoice_value
            ')
            ->first();

        // Get data for previous period (same duration)
        $periodDays = $dateFrom->diffInDays($dateTo);
        $prevDateTo = $dateFrom->copy()->subDay();
        $prevDateFrom = $prevDateTo->copy()->subDays($periodDays);
        
        $previousData = Invoice::where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$prevDateFrom, $prevDateTo])
            ->selectRaw('
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_invoice_value
            ')
            ->first();

        // Calculate growth rates
        $revenueGrowth = $previousData->total_revenue > 0 
            ? (($currentData->total_revenue - $previousData->total_revenue) / $previousData->total_revenue) * 100 
            : 0;
        
        $volumeGrowth = $previousData->invoice_count > 0 
            ? (($currentData->invoice_count - $previousData->invoice_count) / $previousData->invoice_count) * 100 
            : 0;

        return [
            'summary' => [
                'current_revenue' => $currentData->total_revenue ?? 0,
                'previous_revenue' => $previousData->total_revenue ?? 0,
                'revenue_growth' => $revenueGrowth,
                'current_volume' => $currentData->invoice_count ?? 0,
                'previous_volume' => $previousData->invoice_count ?? 0,
                'volume_growth' => $volumeGrowth,
                'avg_invoice_current' => $currentData->avg_invoice_value ?? 0,
                'avg_invoice_previous' => $previousData->avg_invoice_value ?? 0,
            ],
            'date_range' => [$dateFrom, $dateTo],
            'comparison_range' => [$prevDateFrom, $prevDateTo],
        ];
    }

    /**
     * Generate payment methods report.
     */
    private function generatePaymentMethodsReport($dateFrom, $dateTo)
    {
        $paymentMethods = Invoice::where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->where('payment_status', 'paid')
            ->selectRaw('
                payment_method,
                COUNT(*) as payment_count,
                SUM(total_amount) as total_amount,
                AVG(total_amount) as avg_amount
            ')
            ->groupBy('payment_method')
            ->orderBy('total_amount', 'desc')
            ->get();

        $totalPayments = $paymentMethods->sum('total_amount');
        $totalCount = $paymentMethods->sum('payment_count');

        return [
            'summary' => [
                'total_payments' => $totalPayments,
                'total_count' => $totalCount,
                'methods_used' => $paymentMethods->count(),
                'avg_payment' => $totalCount > 0 ? $totalPayments / $totalCount : 0,
            ],
            'payment_methods' => $paymentMethods,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }

    /**
     * Generate seasonal trends report.
     */
    private function generateSeasonalTrendsReport($dateFrom, $dateTo)
    {
        $monthlyTrends = Invoice::where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->selectRaw('
                MONTH(invoice_date) as month,
                MONTHNAME(invoice_date) as month_name,
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_invoice
            ')
            ->groupBy('month', 'month_name')
            ->orderBy('month')
            ->get();

        $quarterlyTrends = Invoice::where('user_id', $this->user_id)
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->selectRaw('
                QUARTER(invoice_date) as quarter,
                YEAR(invoice_date) as year,
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_revenue
            ')
            ->groupBy('quarter', 'year')
            ->orderBy('year')
            ->orderBy('quarter')
            ->get();

        $peakMonth = $monthlyTrends->sortByDesc('total_revenue')->first();
        $slowMonth = $monthlyTrends->sortBy('total_revenue')->first();

        return [
            'summary' => [
                'peak_month' => $peakMonth->month_name ?? 'N/A',
                'peak_revenue' => $peakMonth->total_revenue ?? 0,
                'slow_month' => $slowMonth->month_name ?? 'N/A',
                'slow_revenue' => $slowMonth->total_revenue ?? 0,
                'seasonal_variance' => $peakMonth && $slowMonth && $slowMonth->total_revenue > 0 
                    ? (($peakMonth->total_revenue - $slowMonth->total_revenue) / $slowMonth->total_revenue) * 100 
                    : 0,
            ],
            'monthly_trends' => $monthlyTrends,
            'quarterly_trends' => $quarterlyTrends,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }
}
