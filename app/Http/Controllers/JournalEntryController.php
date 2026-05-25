<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = JournalEntry::where('user_id', Auth::id())->with('lines.account');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('source_type') && $request->source_type) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->has('is_posted') && $request->is_posted !== '') {
            $query->where('is_posted', $request->boolean('is_posted'));
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $entries = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(20);

        $sourceTypes = [
            'manual' => 'Manual',
            'pos_sale' => 'POS Sale',
            'purchase' => 'Purchase',
            'purchase_payment' => 'Purchase Payment',
            'invoice_payment' => 'Invoice Payment',
            'expense' => 'Expense',
            'reversal' => 'Reversal',
        ];

        return view('journal-entries.index', compact('entries', 'sourceTypes'));
    }

    public function create()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->active()
            ->orderBy('code')
            ->get();

        return view('journal-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required_without:lines.*.credit|numeric|min:0',
            'lines.*.credit' => 'required_without:lines.*.debit|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:255',
        ]);

        // Verify debits = credits
        $totalDebit = collect($validated['lines'])->sum('debit');
        $totalCredit = collect($validated['lines'])->sum('credit');

        if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
            return back()->withErrors(['lines' => 'Total debits must equal total credits.'])->withInput();
        }

        if ($totalDebit == 0 && $totalCredit == 0) {
            return back()->withErrors(['lines' => 'Journal entry must have non-zero amounts.'])->withInput();
        }

        $lines = collect($validated['lines'])->map(function ($line) {
            return [
                'account_id' => $line['account_id'],
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
                'description' => $line['description'] ?? null,
            ];
        })->toArray();

        $accountingService = app(AccountingService::class);
        $accountingService->postEntry([
            'user_id' => Auth::id(),
            'date' => $validated['date'],
            'description' => $validated['description'],
            'source_type' => 'manual',
        ], $lines);

        return redirect()->route('journal-entries.index')
            ->with('success', 'Journal entry posted successfully!');
    }

    public function show(JournalEntry $journalEntry)
    {
        if ($journalEntry->user_id !== Auth::id()) {
            abort(403);
        }

        $journalEntry->load('lines.account');

        return view('journal-entries.show', compact('journalEntry'));
    }

    public function reverse(JournalEntry $journalEntry)
    {
        if ($journalEntry->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$journalEntry->is_posted) {
            return back()->with('error', 'Only posted entries can be reversed.');
        }

        $accountingService = app(AccountingService::class);
        $accountingService->reverseEntry($journalEntry);

        return redirect()->route('journal-entries.index')
            ->with('success', 'Journal entry reversed successfully!');
    }
}
