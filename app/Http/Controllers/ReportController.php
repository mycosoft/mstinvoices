<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PDF;
use Carbon\Carbon;
use DateTime;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->reports();
        
        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Handle type filter
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Handle status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get filter options
        $reportTypes = Report::getReportTypes();
        $statuses = ['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'];
        
        return view('reports.index', compact('reports', 'reportTypes', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $reportTypes = Report::getReportTypes();
        $dateRangeTypes = Report::getDateRangeTypes();
        $clients = Auth::user()->clients()->active()->get();
        
        return view('reports.create', compact('reportTypes', 'dateRangeTypes', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:revenue,client,monthly,yearly,custom,invoice_summary,payment_summary,tax_summary,product_performance,overdue_invoices,cash_flow,profit_loss,top_clients,growth_analysis,payment_methods,seasonal_trends',
            'status' => 'required|in:draft,active,archived',
            'format' => 'required|in:table,chart,summary',
            'chart_type' => 'nullable|in:bar,line,pie,doughnut,area',
            'date_range_type' => 'required|in:today,yesterday,this_week,last_week,this_month,last_month,this_quarter,last_quarter,this_year,last_year,custom',
            'date_from' => 'nullable|date|required_if:date_range_type,custom',
            'date_to' => 'nullable|date|after_or_equal:date_from|required_if:date_range_type,custom',
            'filters' => 'nullable|array',
            'columns' => 'nullable|array',
            'is_scheduled' => 'boolean',
            'schedule_frequency' => 'nullable|in:daily,weekly,monthly,quarterly,yearly|required_if:is_scheduled,true',
            'schedule_time' => 'nullable|date_format:H:i|required_if:is_scheduled,true',
            'auto_email' => 'boolean',
            'email_recipients' => 'nullable|array',
            'email_recipients.*' => 'email',
            'export_formats' => 'nullable|array',
            'export_formats.*' => 'in:pdf,excel,csv',
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['is_scheduled'] = $request->has('is_scheduled');
        $validated['auto_email'] = $request->has('auto_email');
        
        // Calculate next generation time if scheduled
        if ($validated['is_scheduled']) {
            $validated['next_generation_at'] = $this->calculateNextGeneration(
                $validated['schedule_frequency'],
                $validated['schedule_time']
            );
        }
        
        $report = Report::create($validated);
        
        return redirect()->route('reports.show', $report)
                        ->with('success', 'Report created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Generate fresh report data
        $reportData = $report->generateData();
        
        // Update last generation tracking
        $report->updateGenerationTracking();
        
        return view('reports.show', compact('report', 'reportData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $reportTypes = Report::getReportTypes();
        $dateRangeTypes = Report::getDateRangeTypes();
        $clients = Auth::user()->clients()->active()->get();
        
        return view('reports.edit', compact('report', 'reportTypes', 'dateRangeTypes', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:revenue,client,monthly,yearly,custom,invoice_summary,payment_summary,tax_summary,product_performance,overdue_invoices,cash_flow,profit_loss,top_clients,growth_analysis,payment_methods,seasonal_trends',
            'status' => 'required|in:draft,active,archived',
            'format' => 'required|in:table,chart,summary',
            'chart_type' => 'nullable|in:bar,line,pie,doughnut,area',
            'date_range_type' => 'required|in:today,yesterday,this_week,last_week,this_month,last_month,this_quarter,last_quarter,this_year,last_year,custom',
            'date_from' => 'nullable|date|required_if:date_range_type,custom',
            'date_to' => 'nullable|date|after_or_equal:date_from|required_if:date_range_type,custom',
            'filters' => 'nullable|array',
            'columns' => 'nullable|array',
            'is_scheduled' => 'boolean',
            'schedule_frequency' => 'nullable|in:daily,weekly,monthly,quarterly,yearly|required_if:is_scheduled,true',
            'schedule_time' => 'nullable|date_format:H:i|required_if:is_scheduled,true',
            'auto_email' => 'boolean',
            'email_recipients' => 'nullable|array',
            'email_recipients.*' => 'email',
            'export_formats' => 'nullable|array',
            'export_formats.*' => 'in:pdf,excel,csv',
        ]);
        
        $validated['is_scheduled'] = $request->has('is_scheduled');
        $validated['auto_email'] = $request->has('auto_email');
        
        // Calculate next generation time if scheduled
        if ($validated['is_scheduled']) {
            $validated['next_generation_at'] = $this->calculateNextGeneration(
                $validated['schedule_frequency'],
                $validated['schedule_time']
            );
        } else {
            $validated['next_generation_at'] = null;
        }
        
        $report->update($validated);
        
        return redirect()->route('reports.show', $report)
                        ->with('success', 'Report updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete associated files
        if ($report->report_file_path && Storage::exists($report->report_file_path)) {
            Storage::delete($report->report_file_path);
        }
        
        $report->delete();
        
        return redirect()->route('reports.index')
                        ->with('success', 'Report deleted successfully!');
    }

    /**
     * Generate and run a specific report.
     */
    public function generate(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Generate report data
        $reportData = $report->generateData();
        
        // Store the result
        $report->update(['last_result' => $reportData]);
        
        // Update generation tracking
        $report->updateGenerationTracking();
        
        return redirect()->route('reports.show', $report)
                        ->with('success', 'Report generated successfully!');
    }

    /**
     * Export report to PDF.
     */
    public function exportPdf(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $reportData = $report->generateData();
        
        $pdf = PDF::loadView('reports.pdf', compact('report', 'reportData'));
        
        return $pdf->download('report-' . $report->name . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export report to Excel.
     */
    public function exportExcel(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $reportData = $report->generateData();
        $filename = 'report-' . Str::slug($report->name) . '-' . now()->format('Y-m-d') . '.xlsx';
        
        // Create Excel data based on report type
        $excelData = $this->prepareExcelData($report, $reportData);
        
        // Create a simple Excel file using a basic approach
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];
        
        $callback = function () use ($excelData, $report) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['Report: ' . $report->name]);
            fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Type: ' . ucfirst($report->type)]);
            fputcsv($file, []);
            
            // Write data
            foreach ($excelData as $section) {
                if (isset($section['title'])) {
                    fputcsv($file, [$section['title']]);
                    fputcsv($file, []);
                }
                
                if (isset($section['headers'])) {
                    fputcsv($file, $section['headers']);
                }
                
                if (isset($section['data'])) {
                    foreach ($section['data'] as $row) {
                        fputcsv($file, $row);
                    }
                }
                
                fputcsv($file, []);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export report to CSV.
     */
    public function exportCsv(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $reportData = $report->generateData();
        
        $filename = 'report-' . $report->name . '-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function () use ($reportData, $report) {
            $file = fopen('php://output', 'w');
            
            // Write CSV based on report type
            switch ($report->type) {
                case 'revenue':
                    fputcsv($file, ['Month', 'Year', 'Invoice Count', 'Total Revenue', 'Paid Revenue']);
                    foreach ($reportData['monthly_data'] as $data) {
                        fputcsv($file, [
                            $data->month,
                            $data->year,
                            $data->invoice_count,
                            $data->total_revenue,
                            $data->paid_revenue
                        ]);
                    }
                    break;
                case 'client':
                    fputcsv($file, ['Client Name', 'Company', 'Invoice Count', 'Total Revenue', 'Paid Revenue', 'Outstanding']);
                    foreach ($reportData['client_data'] as $data) {
                        fputcsv($file, [
                            $data->name,
                            $data->company_name,
                            $data->invoice_count,
                            $data->total_revenue,
                            $data->paid_revenue,
                            $data->outstanding_amount
                        ]);
                    }
                    break;
                default:
                    fputcsv($file, ['No data available for CSV export']);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Duplicate a report.
     */
    public function duplicate(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $newReport = $report->replicate();
        $newReport->name = $report->name . ' (Copy)';
        $newReport->status = 'draft';
        $newReport->generation_count = 0;
        $newReport->last_generated_at = null;
        $newReport->next_generation_at = null;
        $newReport->last_result = null;
        $newReport->report_file_path = null;
        $newReport->save();
        
        return redirect()->route('reports.edit', $newReport)
                        ->with('success', 'Report duplicated successfully!');
    }

    /**
     * Calculate next generation time.
     */
    private function calculateNextGeneration($frequency, $time)
    {
        $now = Carbon::now();
        $scheduleTime = Carbon::createFromTimeString($time);
        
        switch ($frequency) {
            case 'daily':
                return $now->addDay()->setTimeFromTimeString($time);
            case 'weekly':
                return $now->addWeek()->setTimeFromTimeString($time);
            case 'monthly':
                return $now->addMonth()->setTimeFromTimeString($time);
            case 'quarterly':
                return $now->addQuarter()->setTimeFromTimeString($time);
            case 'yearly':
                return $now->addYear()->setTimeFromTimeString($time);
            default:
                return null;
        }
    }
    
    /**
     * Show quick revenue report.
     */
    public function quickRevenue(Request $request)
    {
        $dateRange = $this->getQuickDateRange($request->get('range', 'this_month'));
        $reportData = $this->generateQuickRevenueData($dateRange[0], $dateRange[1]);
        
        return view('reports.quick.revenue', compact('reportData', 'dateRange'));
    }
    
    /**
     * Show quick client report.
     */
    public function quickClient(Request $request)
    {
        $dateRange = $this->getQuickDateRange($request->get('range', 'this_year'));
        $reportData = $this->generateQuickClientData($dateRange[0], $dateRange[1]);
        
        return view('reports.quick.client', compact('reportData', 'dateRange'));
    }
    
    /**
     * Show quick monthly report.
     */
    public function quickMonthly(Request $request)
    {
        $year = $request->get('year', now()->year);
        $reportData = $this->generateQuickMonthlyData($year);
        
        return view('reports.quick.monthly', compact('reportData', 'year'));
    }
    
    /**
     * Show quick yearly report.
     */
    public function quickYearly(Request $request)
    {
        $years = $request->get('years', 5); // Last 5 years
        $reportData = $this->generateQuickYearlyData($years);
        
        return view('reports.quick.yearly', compact('reportData', 'years'));
    }
    
    /**
     * Export quick report as PDF.
     */
    public function exportQuickPdf(Request $request)
    {
        $type = $request->get('type');
        $range = $request->get('range', 'this_month');
        
        switch ($type) {
            case 'revenue':
                $dateRange = $this->getQuickDateRange($range);
                $reportData = $this->generateQuickRevenueData($dateRange[0], $dateRange[1]);
                break;
            case 'client':
                $dateRange = $this->getQuickDateRange($range);
                $reportData = $this->generateQuickClientData($dateRange[0], $dateRange[1]);
                break;
            case 'monthly':
                $year = $request->get('year', now()->year);
                $reportData = $this->generateQuickMonthlyData($year);
                break;
            case 'yearly':
                $years = $request->get('years', 5);
                $reportData = $this->generateQuickYearlyData($years);
                break;
            default:
                abort(404);
        }
        
        $pdf = PDF::loadView('reports.quick.pdf', compact('reportData', 'type'));
        return $pdf->download($type . '-report-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Export quick report as Excel.
     */
    public function exportQuickExcel(Request $request)
    {
        $type = $request->get('type');
        $range = $request->get('range', 'this_month');
        
        switch ($type) {
            case 'revenue':
                $dateRange = $this->getQuickDateRange($range);
                $reportData = $this->generateQuickRevenueData($dateRange[0], $dateRange[1]);
                break;
            case 'client':
                $dateRange = $this->getQuickDateRange($range);
                $reportData = $this->generateQuickClientData($dateRange[0], $dateRange[1]);
                break;
            case 'monthly':
                $year = $request->get('year', now()->year);
                $reportData = $this->generateQuickMonthlyData($year);
                break;
            case 'yearly':
                $years = $request->get('years', 5);
                $reportData = $this->generateQuickYearlyData($years);
                break;
            default:
                abort(404);
        }
        
        $filename = $type . '-report-' . now()->format('Y-m-d') . '.xlsx';
        
        // Create Excel response
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];
        
        $callback = function () use ($reportData, $type) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, [ucfirst($type) . ' Report']);
            fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);
            
            // Write data based on report type
            $this->writeQuickExcelData($file, $reportData, $type);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get date range for quick reports.
     */
    private function getQuickDateRange($range)
    {
        $now = Carbon::now();
        
        switch ($range) {
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
            default:
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
        }
    }
    
    /**
     * Generate quick revenue report data.
     */
    private function generateQuickRevenueData($dateFrom, $dateTo)
    {
        $invoices = Invoice::where('user_id', Auth::id())
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
                'payment_rate' => $totalRevenue > 0 ? ($paidRevenue / $totalRevenue) * 100 : 0,
            ],
            'monthly_data' => $monthlyData,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }
    
    /**
     * Generate quick client report data.
     */
    private function generateQuickClientData($dateFrom, $dateTo)
    {
        $clientData = DB::table('clients')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('clients.user_id', Auth::id())
            ->whereBetween('invoices.invoice_date', [$dateFrom, $dateTo])
            ->select(
                'clients.id',
                'clients.name',
                'clients.company_name',
                'clients.email',
                'clients.phone',
                DB::raw('COUNT(invoices.id) as invoice_count'),
                DB::raw('SUM(invoices.total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN invoices.payment_status = "paid" THEN invoices.total_amount ELSE 0 END) as paid_revenue'),
                DB::raw('SUM(invoices.balance_due) as outstanding_amount'),
                DB::raw('AVG(invoices.total_amount) as average_invoice_value')
            )
            ->groupBy('clients.id', 'clients.name', 'clients.company_name', 'clients.email', 'clients.phone')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        $totalClients = Client::where('user_id', Auth::id())->count();
        $activeClients = $clientData->where('invoice_count', '>', 0)->count();
        $totalRevenue = $clientData->sum('total_revenue');
        $averageRevenuePerClient = $activeClients > 0 ? $totalRevenue / $activeClients : 0;
        
        return [
            'summary' => [
                'total_clients' => $totalClients,
                'active_clients' => $activeClients,
                'total_revenue' => $totalRevenue,
                'average_revenue_per_client' => $averageRevenuePerClient,
            ],
            'client_data' => $clientData,
            'date_range' => [$dateFrom, $dateTo],
        ];
    }
    
    /**
     * Generate quick monthly report data.
     */
    private function generateQuickMonthlyData($year)
    {
        $monthlyData = Invoice::where('user_id', Auth::id())
            ->whereYear('invoice_date', $year)
            ->selectRaw('
                MONTH(invoice_date) as month,
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_revenue,
                AVG(total_amount) as average_invoice_value
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $totalRevenue = $monthlyData->sum('total_revenue');
        $totalInvoices = $monthlyData->sum('invoice_count');
        $averageMonthlyRevenue = $monthlyData->count() > 0 ? $totalRevenue / $monthlyData->count() : 0;
        
        return [
            'summary' => [
                'year' => $year,
                'total_revenue' => $totalRevenue,
                'total_invoices' => $totalInvoices,
                'average_monthly_revenue' => $averageMonthlyRevenue,
            ],
            'monthly_data' => $monthlyData,
        ];
    }
    
    /**
     * Generate quick yearly report data.
     */
    private function generateQuickYearlyData($years)
    {
        $endYear = now()->year;
        $startYear = $endYear - $years + 1;
        
        $yearlyData = Invoice::where('user_id', Auth::id())
            ->whereBetween(DB::raw('YEAR(invoice_date)'), [$startYear, $endYear])
            ->selectRaw('
                YEAR(invoice_date) as year,
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_revenue,
                AVG(total_amount) as average_invoice_value
            ')
            ->groupBy('year')
            ->orderBy('year')
            ->get();
        
        $totalRevenue = $yearlyData->sum('total_revenue');
        $totalInvoices = $yearlyData->sum('invoice_count');
        $averageYearlyRevenue = $yearlyData->count() > 0 ? $totalRevenue / $yearlyData->count() : 0;
        
        return [
            'summary' => [
                'years_range' => $startYear . ' - ' . $endYear,
                'total_revenue' => $totalRevenue,
                'total_invoices' => $totalInvoices,
                'average_yearly_revenue' => $averageYearlyRevenue,
            ],
            'yearly_data' => $yearlyData,
        ];
    }
    
    /**
     * Write Excel data for quick reports.
     */
    private function writeQuickExcelData($file, $reportData, $type)
    {
        switch ($type) {
            case 'revenue':
                fputcsv($file, ['Summary']);
                fputcsv($file, ['Total Revenue', 'UGX ' . number_format($reportData['summary']['total_revenue'], 0)]);
                fputcsv($file, ['Paid Revenue', 'UGX ' . number_format($reportData['summary']['paid_revenue'], 0)]);
                fputcsv($file, ['Pending Revenue', 'UGX ' . number_format($reportData['summary']['pending_revenue'], 0)]);
                fputcsv($file, ['Invoice Count', number_format($reportData['summary']['invoice_count'])]);
                fputcsv($file, ['Average Invoice Value', 'UGX ' . number_format($reportData['summary']['average_invoice_value'], 0)]);
                fputcsv($file, ['Payment Rate', number_format($reportData['summary']['payment_rate'], 1) . '%']);
                fputcsv($file, []);
                
                if (isset($reportData['monthly_data'])) {
                    fputcsv($file, ['Monthly Breakdown']);
                    fputcsv($file, ['Month', 'Year', 'Invoice Count', 'Total Revenue', 'Paid Revenue']);
                    foreach ($reportData['monthly_data'] as $data) {
                        fputcsv($file, [
                            DateTime::createFromFormat('!m', $data->month)->format('F'),
                            $data->year,
                            $data->invoice_count,
                            'UGX ' . number_format($data->total_revenue, 0),
                            'UGX ' . number_format($data->paid_revenue, 0)
                        ]);
                    }
                }
                break;
                
            case 'client':
                fputcsv($file, ['Client Performance']);
                fputcsv($file, ['Client Name', 'Company', 'Email', 'Invoice Count', 'Total Revenue', 'Paid Revenue', 'Outstanding', 'Average Invoice']);
                foreach ($reportData['client_data'] as $client) {
                    fputcsv($file, [
                        $client->name,
                        $client->company_name ?: 'Individual',
                        $client->email,
                        $client->invoice_count,
                        'UGX ' . number_format($client->total_revenue, 0),
                        'UGX ' . number_format($client->paid_revenue, 0),
                        'UGX ' . number_format($client->outstanding_amount, 0),
                        'UGX ' . number_format($client->average_invoice_value, 0)
                    ]);
                }
                break;
        }
    }
    
    /**
     * Show reports dashboard with scheduling information.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get report statistics
        $stats = [
            'total_reports' => $user->reports()->count(),
            'active_reports' => $user->reports()->where('status', 'active')->count(),
            'scheduled_reports' => $user->reports()->where('is_scheduled', true)->count(),
            'draft_reports' => $user->reports()->where('status', 'draft')->count(),
        ];
        
        // Get upcoming scheduled reports
        $upcomingReports = $user->reports()
            ->where('is_scheduled', true)
            ->where('status', 'active')
            ->whereNotNull('next_generation_at')
            ->orderBy('next_generation_at')
            ->take(10)
            ->get();
        
        // Get recently generated reports
        $recentReports = $user->reports()
            ->whereNotNull('last_generated_at')
            ->orderBy('last_generated_at', 'desc')
            ->take(10)
            ->get();
        
        // Get generation statistics for the last 30 days
        $generationStats = $user->reports()
            ->where('last_generated_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(last_generated_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Business analytics for quick insights
        $businessInsights = $this->generateBusinessInsights();
        
        // Report type distribution
        $reportTypeStats = $user->reports()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
        
        // Monthly generation trend
        $monthlyTrend = $user->reports()
            ->whereNotNull('last_generated_at')
            ->where('last_generated_at', '>=', now()->subMonths(6))
            ->selectRaw('YEAR(last_generated_at) as year, MONTH(last_generated_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        return view('reports.dashboard', compact(
            'stats',
            'upcomingReports',
            'recentReports',
            'generationStats',
            'businessInsights',
            'reportTypeStats',
            'monthlyTrend'
        ));
    }
    
    /**
     * Generate business insights for dashboard.
     */
    private function generateBusinessInsights()
    {
        $user = Auth::user();
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        // Revenue insights
        $currentRevenue = Invoice::where('user_id', $user->id)
            ->where('invoice_date', '>=', $currentMonth)
            ->sum('total_amount');
            
        $lastRevenue = Invoice::where('user_id', $user->id)
            ->whereBetween('invoice_date', [$lastMonth, $currentMonth])
            ->sum('total_amount');
            
        $revenueGrowth = $lastRevenue > 0 ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
        
        // Invoice insights
        $currentInvoices = Invoice::where('user_id', $user->id)
            ->where('invoice_date', '>=', $currentMonth)
            ->count();
            
        $lastInvoices = Invoice::where('user_id', $user->id)
            ->whereBetween('invoice_date', [$lastMonth, $currentMonth])
            ->count();
            
        $invoiceGrowth = $lastInvoices > 0 ? (($currentInvoices - $lastInvoices) / $lastInvoices) * 100 : 0;
        
        // Client insights
        $totalClients = Client::where('user_id', $user->id)->count();
        $activeClients = Client::where('user_id', $user->id)
            ->whereHas('invoices', function($q) use ($currentMonth) {
                $q->where('invoice_date', '>=', $currentMonth);
            })
            ->count();
            
        // Payment insights
        $overdueAmount = Invoice::where('user_id', $user->id)
            ->where('due_date', '<', now())
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->sum('balance_due');
            
        $overdueCount = Invoice::where('user_id', $user->id)
            ->where('due_date', '<', now())
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->count();
        
        // Collection rate
        $totalInvoiced = Invoice::where('user_id', $user->id)
            ->where('invoice_date', '>=', now()->subDays(30))
            ->sum('total_amount');
            
        $totalPaid = Invoice::where('user_id', $user->id)
            ->where('invoice_date', '>=', now()->subDays(30))
            ->where('payment_status', 'paid')
            ->sum('total_amount');
            
        $collectionRate = $totalInvoiced > 0 ? ($totalPaid / $totalInvoiced) * 100 : 0;
        
        // Top performing client this month
        $topClient = DB::table('clients')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('clients.user_id', $user->id)
            ->where('invoices.invoice_date', '>=', $currentMonth)
            ->select('clients.name', DB::raw('SUM(invoices.total_amount) as total_revenue'))
            ->groupBy('clients.id', 'clients.name')
            ->orderBy('total_revenue', 'desc')
            ->first();
        
        return [
            'current_revenue' => $currentRevenue,
            'revenue_growth' => $revenueGrowth,
            'current_invoices' => $currentInvoices,
            'invoice_growth' => $invoiceGrowth,
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'overdue_amount' => $overdueAmount,
            'overdue_count' => $overdueCount,
            'collection_rate' => $collectionRate,
            'top_client' => $topClient,
        ];
    }
    
    /**
     * Test scheduled report generation for a specific report.
     */
    public function testScheduled(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            // Generate the report
            $reportData = $report->generateData();
            
            // Update generation tracking
            $report->updateGenerationTracking();
            
            // Send test email if configured
            if ($report->auto_email && !empty($report->email_recipients)) {
                // Add current user's email to test recipients
                $testRecipients = array_merge($report->email_recipients, [Auth::user()->email]);
                
                // Temporarily modify recipients for testing
                $originalRecipients = $report->email_recipients;
                $report->email_recipients = array_unique($testRecipients);
                
                $this->sendTestReportEmail($report, $reportData);
                
                // Restore original recipients
                $report->email_recipients = $originalRecipients;
                $report->save();
            }
            
            return redirect()->route('reports.show', $report)
                            ->with('success', 'Test report generation completed! Check your email for the report.');
            
        } catch (\Exception $e) {
            return redirect()->route('reports.show', $report)
                            ->with('error', 'Test generation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Send test report email.
     */
    private function sendTestReportEmail(Report $report, array $reportData)
    {
        // Generate PDF for email attachment
        $pdf = PDF::loadView('reports.pdf', compact('report', 'reportData'));
        $pdfContent = $pdf->output();
        
        $filename = 'test-report-' . \Str::slug($report->name) . '-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        // Store PDF temporarily
        $tempPath = 'temp/reports/' . $filename;
        Storage::put($tempPath, $pdfContent);
        
        try {
            Mail::send('emails.scheduled-report', compact('report', 'reportData'), function ($message) use ($report, $tempPath, $filename) {
                $message->to($report->email_recipients)
                       ->subject('[TEST] Scheduled Report: ' . $report->name)
                       ->attach(Storage::path($tempPath), [
                           'as' => $filename,
                           'mime' => 'application/pdf'
                       ]);
            });
        } finally {
            // Clean up temporary file after a delay
            if (Storage::exists($tempPath)) {
                // Delete after 1 hour to allow for email delivery
                Storage::delete($tempPath);
            }
        }
    }
    
    /**
     * Prepare Excel data based on report type.
     */
    private function prepareExcelData(Report $report, array $reportData)
    {
        $excelData = [];
        
        switch ($report->type) {
            case 'revenue':
                $excelData[] = [
                    'title' => 'Revenue Summary',
                    'headers' => ['Metric', 'Value'],
                    'data' => [
                        ['Total Revenue', 'UGX ' . number_format($reportData['summary']['total_revenue'], 0)],
                        ['Paid Revenue', 'UGX ' . number_format($reportData['summary']['paid_revenue'], 0)],
                        ['Pending Revenue', 'UGX ' . number_format($reportData['summary']['pending_revenue'], 0)],
                        ['Invoice Count', number_format($reportData['summary']['invoice_count'])],
                        ['Average Invoice Value', 'UGX ' . number_format($reportData['summary']['average_invoice_value'], 0)],
                        ['Payment Rate', number_format(($reportData['summary']['paid_revenue'] / max(1, $reportData['summary']['total_revenue'])) * 100, 1) . '%']
                    ]
                ];
                
                if (isset($reportData['monthly_data'])) {
                    $excelData[] = [
                        'title' => 'Monthly Revenue Breakdown',
                        'headers' => ['Month', 'Year', 'Invoice Count', 'Total Revenue', 'Paid Revenue', 'Payment Rate'],
                        'data' => collect($reportData['monthly_data'])->map(function ($data) {
                            return [
                                DateTime::createFromFormat('!m', $data->month)->format('F'),
                                $data->year,
                                $data->invoice_count,
                                'UGX ' . number_format($data->total_revenue, 0),
                                'UGX ' . number_format($data->paid_revenue, 0),
                                number_format(($data->paid_revenue / max(1, $data->total_revenue)) * 100, 1) . '%'
                            ];
                        })->toArray()
                    ];
                }
                break;
                
            case 'client':
                $excelData[] = [
                    'title' => 'Client Performance Summary',
                    'headers' => ['Metric', 'Value'],
                    'data' => [
                        ['Total Clients', number_format($reportData['summary']['total_clients'])],
                        ['Active Clients', number_format($reportData['summary']['active_clients'])],
                        ['Total Revenue', 'UGX ' . number_format($reportData['summary']['total_revenue'], 0)],
                        ['Average Revenue per Client', 'UGX ' . number_format($reportData['summary']['average_revenue_per_client'], 0)],
                        ['Average Payment Days', number_format($reportData['summary']['average_payment_days'], 0)]
                    ]
                ];
                
                if (isset($reportData['top_clients'])) {
                    $excelData[] = [
                        'title' => 'Top Clients',
                        'headers' => ['Client Name', 'Company', 'Total Revenue', 'Invoice Count', 'Average Invoice Value', 'Payment Rate'],
                        'data' => collect($reportData['top_clients'])->map(function ($client) {
                            return [
                                $client->name,
                                $client->company ?? 'Individual',
                                'UGX ' . number_format($client->total_revenue, 0),
                                $client->invoice_count,
                                'UGX ' . number_format($client->average_invoice_value, 0),
                                number_format(($client->paid_revenue / max(1, $client->total_revenue)) * 100, 1) . '%'
                            ];
                        })->toArray()
                    ];
                }
                break;
                
            case 'monthly':
            case 'yearly':
                $excelData[] = [
                    'title' => ucfirst($report->type) . ' Performance Summary',
                    'headers' => ['Metric', 'Value'],
                    'data' => [
                        ['Total Revenue', 'UGX ' . number_format($reportData['summary']['total_revenue'], 0)],
                        ['Total Invoices', number_format($reportData['summary']['total_invoices'])],
                        ['Average Period Revenue', 'UGX ' . number_format($reportData['summary']['average_period_revenue'], 0)],
                        ['Growth Rate', number_format($reportData['summary']['growth_rate'], 1) . '%']
                    ]
                ];
                
                if (isset($reportData['period_data'])) {
                    $headers = $report->type === 'monthly' ? 
                        ['Month', 'Year', 'Invoice Count', 'Total Revenue', 'Paid Revenue'] :
                        ['Year', 'Invoice Count', 'Total Revenue', 'Paid Revenue'];
                        
                    $excelData[] = [
                        'title' => ucfirst($report->type) . ' Breakdown',
                        'headers' => $headers,
                        'data' => collect($reportData['period_data'])->map(function ($period) use ($report) {
                            $data = [
                                $period->invoice_count,
                                'UGX ' . number_format($period->total_revenue, 0),
                                'UGX ' . number_format($period->paid_revenue, 0)
                            ];
                            
                            if ($report->type === 'monthly') {
                                array_unshift($data, DateTime::createFromFormat('!m', $period->month)->format('F'), $period->year);
                            } else {
                                array_unshift($data, $period->year);
                            }
                            
                            return $data;
                        })->toArray()
                    ];
                }
                break;
                
            default:
                $excelData[] = [
                    'title' => 'Report Data',
                    'headers' => ['Information', 'Value'],
                    'data' => [
                        ['Report Name', $report->name],
                        ['Report Type', ucfirst($report->type)],
                        ['Generated At', now()->format('Y-m-d H:i:s')],
                        ['Status', ucfirst($report->status)]
                    ]
                ];
        }
        
        return $excelData;
    }

    /**
     * Quick Product Performance Report.
     */
    public function quickProductPerformance(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'product_performance',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.product_performance', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Quick Overdue Invoices Report.
     */
    public function quickOverdueInvoices(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'overdue_invoices',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.overdue_invoices', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Quick Cash Flow Report.
     */
    public function quickCashFlow(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(90)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'cash_flow',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.cash_flow', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Quick Profit & Loss Report.
     */
    public function quickProfitLoss(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(90)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'profit_loss',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.profit_loss', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Quick Top Clients Report.
     */
    public function quickTopClients(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(365)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'top_clients',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.top_clients', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Quick Growth Analysis Report.
     */
    public function quickGrowthAnalysis(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(90)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'growth_analysis',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.growth_analysis', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Quick Payment Methods Report.
     */
    public function quickPaymentMethods(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(90)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'payment_methods',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.payment_methods', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Quick Seasonal Trends Report.
     */
    public function quickSeasonalTrends(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subYear()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'seasonal_trends',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.seasonal_trends', compact('data', 'dateFrom', 'dateTo'));
    }
    
    /**
     * Quick Payment Status Report.
     */
    public function quickPaymentStatus(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(90)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'payment_summary',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.payment_status', compact('data', 'dateFrom', 'dateTo'));
    }
    
    /**
     * Quick Outstanding Payments Report.
     */
    public function quickOutstanding(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(90)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $report = new Report([
            'type' => 'overdue_invoices',
            'user_id' => Auth::id(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_range_type' => 'custom'
        ]);
        
        $data = $report->generateData();
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('reports.quick.outstanding', compact('data', 'dateFrom', 'dateTo'));
    }
}
