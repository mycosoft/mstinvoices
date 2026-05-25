<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Account::where('user_id', Auth::id())->with('parent');

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('code')->get();
        $accountTypes = Account::getAccountTypes();

        // Group by type
        $groupedAccounts = $accounts->groupBy('type');

        return view('accounts.index', compact('accounts', 'groupedAccounts', 'accountTypes'));
    }

    public function create()
    {
        $accountTypes = Account::getAccountTypes();
        $parentAccounts = Account::where('user_id', Auth::id())->active()->orderBy('code')->get();

        return view('accounts.create', compact('accountTypes', 'parentAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'subtype' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'normal_balance' => 'required|in:debit,credit',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['current_balance'] = $validated['opening_balance'];

        // Check unique code per user
        if (Account::where('user_id', Auth::id())->where('code', $validated['code'])->exists()) {
            return back()->withErrors(['code' => 'This account code already exists.'])->withInput();
        }

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully!');
    }

    public function show(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $account->load(['journalEntryLines.journalEntry' => function ($q) {
            $q->orderBy('date', 'desc');
        }]);

        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $accountTypes = Account::getAccountTypes();
        $parentAccounts = Account::where('user_id', Auth::id())
            ->active()
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();

        return view('accounts.edit', compact('account', 'accountTypes', 'parentAccounts'));
    }

    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'subtype' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'normal_balance' => 'required|in:debit,credit',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'status' => 'required|in:active,inactive',
        ]);

        if (Account::where('user_id', Auth::id())->where('code', $validated['code'])->where('id', '!=', $account->id)->exists()) {
            return back()->withErrors(['code' => 'This account code already exists.'])->withInput();
        }

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully!');
    }

    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        if ($account->is_system) {
            return back()->with('error', 'System accounts cannot be deleted.');
        }

        if ($account->journalEntryLines()->exists()) {
            return back()->with('error', 'Cannot delete account with existing journal entries.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully!');
    }

    public function initialize()
    {
        $userId = Auth::id();

        if (Account::where('user_id', $userId)->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('info', 'Chart of accounts already initialized.');
        }

        \Database\Seeders\ChartOfAccountsSeeder::seedForUser($userId);

        return redirect()->route('accounts.index')
            ->with('success', 'Chart of accounts initialized with default accounts!');
    }
}
