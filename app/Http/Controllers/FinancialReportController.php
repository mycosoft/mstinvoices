<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function trialBalance(Request $request)
    {
        $service = app(AccountingService::class);
        $asOfDate = $request->as_of_date ?? now()->toDateString();
        $data = $service->getTrialBalance(Auth::id(), $asOfDate);

        return view('financial-reports.trial-balance', $data + ['as_of_date' => $asOfDate]);
    }

    public function incomeStatement(Request $request)
    {
        $service = app(AccountingService::class);
        $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();
        $data = $service->getIncomeStatement(Auth::id(), $fromDate, $toDate);

        return view('financial-reports.income-statement', $data);
    }

    public function balanceSheet(Request $request)
    {
        $service = app(AccountingService::class);
        $asOfDate = $request->as_of_date ?? now()->toDateString();
        $data = $service->getBalanceSheet(Auth::id(), $asOfDate);

        return view('financial-reports.balance-sheet', $data + ['as_of_date' => $asOfDate]);
    }

    public function cashFlow(Request $request)
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();

        $cashAccount = Account::where('user_id', Auth::id())->where('code', '1000')->first();
        $userId = Auth::id();

        // Operating activities: income - expenses (cash-based)
        $incomeTotal = JournalEntryLine::whereHas('journalEntry', function ($q) use ($userId, $fromDate, $toDate) {
            $q->where('user_id', $userId)->where('is_posted', true)->whereBetween('date', [$fromDate, $toDate]);
        })->whereHas('account', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('type', 'income');
        })->sum('credit');

        $expenseTotal = JournalEntryLine::whereHas('journalEntry', function ($q) use ($userId, $fromDate, $toDate) {
            $q->where('user_id', $userId)->where('is_posted', true)->whereBetween('date', [$fromDate, $toDate]);
        })->whereHas('account', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('type', 'expense');
        })->sum('debit');

        $operatingCashFlow = $incomeTotal - $expenseTotal;

        // Investing activities (placeholder)
        $investingCashFlow = 0;

        // Financing activities (placeholder - owner contributions, etc.)
        $financingCashFlow = 0;

        // Opening cash balance
        $openingCash = $cashAccount ? $cashAccount->opening_balance : 0;

        // Cash transactions in period
        $cashInflows = JournalEntryLine::whereHas('journalEntry', function ($q) use ($userId, $fromDate, $toDate) {
            $q->where('user_id', $userId)->where('is_posted', true)->whereBetween('date', [$fromDate, $toDate]);
        })->where('account_id', $cashAccount?->id)->sum('debit');

        $cashOutflows = JournalEntryLine::whereHas('journalEntry', function ($q) use ($userId, $fromDate, $toDate) {
            $q->where('user_id', $userId)->where('is_posted', true)->whereBetween('date', [$fromDate, $toDate]);
        })->where('account_id', $cashAccount?->id)->sum('credit');

        $netCashFlow = $cashInflows - $cashOutflows;
        $closingCash = $openingCash + $netCashFlow;

        return view('financial-reports.cash-flow', compact(
            'fromDate', 'toDate',
            'operatingCashFlow', 'investingCashFlow', 'financingCashFlow',
            'openingCash', 'closingCash', 'cashInflows', 'cashOutflows', 'netCashFlow'
        ));
    }

    public function generalLedger(Request $request)
    {
        $accounts = Account::where('user_id', Auth::id())->active()->orderBy('code')->get();

        $accountId = $request->account_id;
        $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();
        $entries = collect();
        $selectedAccount = null;
        $openingBalance = 0;
        $closingBalance = 0;

        if ($accountId) {
            $service = app(AccountingService::class);
            $data = $service->getGeneralLedger(Auth::id(), $accountId, $fromDate, $toDate);
            $selectedAccount = $data['account'];
            $entries = $data['entries'];
            $openingBalance = $data['opening_balance'];
            $closingBalance = $data['closing_balance'];
        }

        return view('financial-reports.general-ledger', compact('accounts', 'selectedAccount', 'entries', 'fromDate', 'toDate', 'openingBalance', 'closingBalance'));
    }

    public function journalReport(Request $request)
    {
        $query = JournalEntry::where('user_id', Auth::id())->with(['lines.account', 'user']);

        if ($request->from_date) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->where('date', '<=', $request->to_date);
        }
        if ($request->source_type) {
            $query->where('source_type', $request->source_type);
        }

        $entries = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(25);

        $sourceTypes = [
            '' => 'All Sources',
            'pos_sale' => 'POS Sale',
            'purchase' => 'Purchase',
            'purchase_payment' => 'Purchase Payment',
            'invoice_payment' => 'Invoice Payment',
            'expense' => 'Expense',
            'manual' => 'Manual Entry',
            'reversal' => 'Reversal',
        ];

        return view('financial-reports.journal-report', compact('entries', 'sourceTypes'));
    }

    public function exportPdf(Request $request, $report)
    {
        $view = match($report) {
            'trial-balance' => 'financial-reports.trial-balance-pdf',
            'income-statement' => 'financial-reports.income-statement-pdf',
            'balance-sheet' => 'financial-reports.balance-sheet-pdf',
            'cash-flow' => 'financial-reports.cash-flow-pdf',
            default => null,
        };

        if (!$view) {
            return back()->with('error', 'Invalid report type.');
        }

        $service = app(AccountingService::class);
        $data = [];

        if ($report === 'trial-balance') {
            $data = $service->getTrialBalance(Auth::id(), $request->as_of_date ?? now()->toDateString());
        } elseif ($report === 'income-statement') {
            $data = $service->getIncomeStatement(Auth::id(), $request->from_date ?? now()->startOfMonth()->toDateString(), $request->to_date ?? now()->toDateString());
        } elseif ($report === 'balance-sheet') {
            $data = $service->getBalanceSheet(Auth::id(), $request->as_of_date ?? now()->toDateString());
        } elseif ($report === 'cash-flow') {
            $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
            $toDate = $request->to_date ?? now()->toDateString();
            return $this->cashFlowExport($fromDate, $toDate);
        }

        $pdf = app('dompdf.wrapper')->loadView($view, $data);
        return $pdf->download($report . '-' . now()->format('Y-m-d') . '.pdf');
    }

    private function cashFlowExport($fromDate, $toDate)
    {
        $data = [];
        $pdf = app('dompdf.wrapper')->loadView('financial-reports.cash-flow-pdf', $data);
        return $pdf->download('cash-flow-' . now()->format('Y-m-d') . '.pdf');
    }
}
