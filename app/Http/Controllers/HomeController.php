<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Quotation;
use App\Models\Expense;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $settings = Setting::forUser($user->id);
        
        // Get all invoices at once for efficient statistics calculation
        $allInvoices = Invoice::where('user_id', $user->id)
            ->select(['payment_status', 'status', 'total_amount', 'paid_amount', 'balance_due', 'invoice_date', 'due_date', 'paid_date', 'created_at'])
            ->get();
        
        // Pre-filter collections for efficiency
        $paidInvoices = $allInvoices->where('payment_status', 'paid');
        $partialInvoices = $allInvoices->where('payment_status', 'partial');
        $unpaidPartialInvoices = $allInvoices->whereIn('payment_status', ['unpaid', 'partial']);
        
        // Calculate basic statistics from pre-filtered collections
        $totalInvoices = $allInvoices->count();
        $paidCount = $paidInvoices->count();
        $pendingInvoices = $unpaidPartialInvoices->count();
        $totalRevenue = $paidInvoices->sum('total_amount') + $partialInvoices->sum('paid_amount');
        
        // Calculate derived statistics
        $pendingAmount = $unpaidPartialInvoices->sum('balance_due');
        
        // Get this month's data efficiently
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Calculate this month revenue from fully paid invoices
        $thisMonthPaidRevenue = $paidInvoices->filter(function($invoice) use ($thisMonth) {
            return $invoice->paid_date && $invoice->paid_date >= $thisMonth;
        })->sum('total_amount');
        
        // Calculate this month revenue from partial payments
        $thisMonthPartialRevenue = $partialInvoices->filter(function($invoice) use ($thisMonth) {
            return $invoice->updated_at && $invoice->updated_at >= $thisMonth;
        })->sum('paid_amount');
        
        // Total this month revenue (paid + partial payments)
        $thisMonthRevenue = $thisMonthPaidRevenue + $thisMonthPartialRevenue;
        
        // Get overdue invoices efficiently
        $now = Carbon::now();
        $overdueCollection = $unpaidPartialInvoices->where('due_date', '<', $now);
        $overdueInvoices = $overdueCollection->count();
        $overdueAmount = $overdueCollection->sum('balance_due');
        
        // Get monthly revenue data for chart (last 6 months) - optimized
        $monthlyRevenue = [];
        $monthlyExpenses = [];
        $monthLabels = [];
        
        // Get all expenses for calculations
        $allExpenses = Expense::where('user_id', $user->id)
            ->select(['payment_status', 'status', 'amount', 'expense_date', 'created_at'])
            ->get();
            
        // Calculate expense statistics
        $totalExpenses = $allExpenses->sum('amount');
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            // Calculate revenue from fully paid invoices in this month
            $paidRevenueInMonth = $paidInvoices->filter(function($invoice) use ($monthStart, $monthEnd) {
                return $invoice->invoice_date && $invoice->invoice_date >= $monthStart && $invoice->invoice_date <= $monthEnd;
            })->sum('total_amount');
            
            // Add revenue from partial payments in this month
            $partialRevenueInMonth = $partialInvoices->filter(function($invoice) use ($monthStart, $monthEnd) {
                return $invoice->invoice_date && $invoice->invoice_date >= $monthStart && $invoice->invoice_date <= $monthEnd;
            })->sum('paid_amount');
            
            // Calculate expenses for this month
            $expensesInMonth = $allExpenses->filter(function($expense) use ($monthStart, $monthEnd) {
                return $expense->expense_date && $expense->expense_date >= $monthStart && $expense->expense_date <= $monthEnd;
            })->sum('amount');
            
            $monthlyRevenue[] = $paidRevenueInMonth + $partialRevenueInMonth;
            $monthlyExpenses[] = $expensesInMonth;
            $monthLabels[] = $month->format('M Y');
        }
        
        // Get payment status distribution from pre-filtered collections
        $paymentStatus = [
            'paid' => $paidCount,
            'unpaid' => $allInvoices->where('payment_status', 'unpaid')->count(),
            'partial' => $allInvoices->where('payment_status', 'partial')->count(),
        ];
        
        // Batch load recent data with relationships
        $recentInvoices = Invoice::where('user_id', $user->id)
            ->with('client:id,name')
            ->select(['id', 'client_id', 'invoice_number', 'total_amount', 'status', 'payment_status', 'invoice_date', 'created_at'])
            ->latest('created_at')
            ->limit(5)
            ->get();
            
        $recentPayments = Invoice::where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->where(function($query) {
                $query->where(function($q) {
                      $q->where('payment_status', 'paid')
                        ->where('paid_date', '>=', Carbon::now()->subDays(7));
                  })
                  ->orWhere(function($q) {
                      $q->where('payment_status', 'partial')
                        ->where('updated_at', '>=', Carbon::now()->subDays(7));
                  });
            })
            ->with('client:id,name')
            ->select(['id', 'client_id', 'invoice_number', 'total_amount', 'paid_amount', 'payment_status', 'paid_date', 'updated_at'])
            ->latest('updated_at')
            ->limit(5)
            ->get();
            
        $recentQuotations = Quotation::where('user_id', $user->id)
            ->with('client:id,name')
            ->select(['id', 'client_id', 'quotation_number', 'total_amount', 'status', 'created_at'])
            ->latest('created_at')
            ->limit(5)
            ->get();
        
        // Calculate additional statistics efficiently
        $totalClients = Client::where('user_id', $user->id)->count();
        
        // Get project statistics
        $allProjects = \App\Models\Project::where('user_id', $user->id)->get();
        $totalProjects = $allProjects->count();
        $activeProjects = $allProjects->whereIn('status', ['pending', 'in_progress'])->count();
        $completedProjects = $allProjects->where('status', 'completed')->count();
        $cancelledProjects = $allProjects->where('status', 'cancelled')->count();
        
        // Get top clients efficiently
        $topClients = Client::where('user_id', $user->id)
            ->withCount('invoices')
            ->with(['invoices' => function($query) {
                $query->whereIn('payment_status', ['paid', 'partial'])
                      ->select(['client_id', 'total_amount', 'paid_amount', 'payment_status']);
            }])
            ->select(['id', 'name'])
            ->get()
            ->map(function($client) {
                // Calculate total revenue including partial payments
                $client->total_revenue = $client->invoices->sum(function($invoice) {
                    return $invoice->payment_status === 'paid' ? $invoice->total_amount : $invoice->paid_amount;
                });
                return $client;
            })
            ->sortByDesc('total_revenue')
            ->take(5);
            
        // This week statistics - optimized
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisWeekPaidRevenue = $paidInvoices
            ->filter(function($invoice) use ($thisWeekStart) {
                return $invoice->paid_date && $invoice->paid_date >= $thisWeekStart;
            })
            ->sum('total_amount');
            
        $thisWeekPartialRevenue = $partialInvoices
            ->filter(function($invoice) use ($thisWeekStart) {
                return $invoice->updated_at && $invoice->updated_at >= $thisWeekStart;
            })
            ->sum('paid_amount');
            
        $thisWeekRevenue = $thisWeekPaidRevenue + $thisWeekPartialRevenue;
        $thisWeekInvoices = $allInvoices->where('created_at', '>=', $thisWeekStart)->count();
        
        return view('home', compact(
            'totalInvoices', 'paidCount', 'pendingInvoices', 'totalRevenue',
            'thisMonthRevenue', 'overdueInvoices', 'overdueAmount',
            'monthlyRevenue', 'monthlyExpenses', 'monthLabels', 'recentInvoices', 'paymentStatus',
            'topClients', 'totalClients', 'pendingAmount', 'recentPayments', 'recentQuotations',
            'thisWeekRevenue', 'thisWeekInvoices', 'settings', 'totalExpenses',
            'totalProjects', 'activeProjects', 'completedProjects', 'cancelledProjects'
        ));
    }
}