<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Create and post a journal entry with multiple lines.
     */
    public function postEntry(array $data, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($data, $lines) {
            $totalDebit = collect($lines)->sum('debit');
            $totalCredit = collect($lines)->sum('credit');

            $entry = JournalEntry::create(array_merge($data, [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_posted' => true,
                'posted_at' => now(),
            ]));

            foreach ($lines as $line) {
                $entry->lines()->create($line);

                // Update account balance
                $account = Account::find($line['account_id']);
                if ($account) {
                    $account->updateBalance($line['debit'] ?? 0, $line['credit'] ?? 0);
                }
            }

            return $entry;
        });
    }

    /**
     * Reverse a posted journal entry.
     */
    public function reverseEntry(JournalEntry $entry): JournalEntry
    {
        return DB::transaction(function () use ($entry) {
            $reversalLines = [];
            foreach ($entry->lines as $line) {
                $reversalLines[] = [
                    'account_id' => $line->account_id,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'description' => 'Reversal of ' . $entry->reference_number,
                ];

                // Reverse the account balance
                $line->account->updateBalance($line->credit, $line->debit);
            }

            $reversal = JournalEntry::create([
                'user_id' => $entry->user_id,
                'date' => now()->toDateString(),
                'description' => 'Reversal: ' . $entry->description,
                'source_type' => 'reversal',
                'source_id' => $entry->id,
                'total_debit' => $entry->total_credit,
                'total_credit' => $entry->total_debit,
                'is_posted' => true,
                'posted_at' => now(),
            ]);

            foreach ($reversalLines as $line) {
                $reversal->lines()->create($line);
            }

            return $reversal;
        });
    }

    /**
     * Post a POS sale: Debit Cash/Receivable, Credit Sales Revenue + Tax Payable.
     * If inventory tracked: Debit COGS, Credit Inventory.
     */
    public function postPosSale(float $totalAmount, float $taxAmount, float $discountAmount, float $cogsAmount, string $paymentMethod, ?int $clientId, string $sourceId): JournalEntry
    {
        $userId = Auth::id();
        $lines = [];

        // Debit: Cash or Accounts Receivable
        $cashAccount = $this->getSystemAccount($userId, '1000'); // Cash
        $arAccount = $this->getSystemAccount($userId, '1100'); // Accounts Receivable
        $salesAccount = $this->getSystemAccount($userId, '4000'); // Sales Revenue
        $taxAccount = $this->getSystemAccount($userId, '2100'); // Tax Payable
        $discountAccount = $this->getSystemAccount($userId, '4300'); // Sales Discounts
        $cogsAccount = $this->getSystemAccount($userId, '5000'); // COGS
        $inventoryAccount = $this->getSystemAccount($userId, '1200'); // Inventory

        $netAmount = $totalAmount - $taxAmount;

        // Payment received
        if ($paymentMethod !== 'credit') {
            $lines[] = ['account_id' => $cashAccount->id, 'debit' => $totalAmount, 'credit' => 0, 'description' => 'POS Sale'];
        } else {
            $lines[] = ['account_id' => $arAccount->id, 'debit' => $totalAmount, 'credit' => 0, 'description' => 'POS Sale on credit'];
        }

        // Credit: Sales Revenue
        $lines[] = ['account_id' => $salesAccount->id, 'debit' => 0, 'credit' => $netAmount + $discountAmount, 'description' => 'Sales Revenue'];

        // Credit: Tax Payable
        if ($taxAmount > 0) {
            $lines[] = ['account_id' => $taxAccount->id, 'debit' => 0, 'credit' => $taxAmount, 'description' => 'Tax collected'];
        }

        // Debit: Sales Discounts
        if ($discountAmount > 0) {
            $lines[] = ['account_id' => $discountAccount->id, 'debit' => $discountAmount, 'credit' => 0, 'description' => 'Sales discount'];
        }

        // COGS entry if inventory tracked
        if ($cogsAmount > 0) {
            $lines[] = ['account_id' => $cogsAccount->id, 'debit' => $cogsAmount, 'credit' => 0, 'description' => 'Cost of goods sold'];
            $lines[] = ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $cogsAmount, 'description' => 'Inventory reduced'];
        }

        return $this->postEntry([
            'user_id' => $userId,
            'date' => now()->toDateString(),
            'description' => 'POS Sale',
            'source_type' => 'pos_sale',
            'source_id' => $sourceId,
        ], $lines);
    }

    /**
     * Post a purchase receipt: Debit Inventory/Expense, Credit Accounts Payable.
     */
    public function postPurchaseReceipt(float $totalAmount, float $taxAmount, float $inventoryAmount, float $expenseAmount, string $sourceId): JournalEntry
    {
        $userId = Auth::id();
        $lines = [];

        $inventoryAccount = $this->getSystemAccount($userId, '1200');
        $taxAccount = $this->getSystemAccount($userId, '2100');
        $apAccount = $this->getSystemAccount($userId, '2000');
        $purchasesAccount = $this->getSystemAccount($userId, '5100');

        // Debit: Inventory
        if ($inventoryAmount > 0) {
            $lines[] = ['account_id' => $inventoryAccount->id, 'debit' => $inventoryAmount, 'credit' => 0, 'description' => 'Inventory from purchase'];
        }

        // Debit: Purchases (expense portion)
        if ($expenseAmount > 0) {
            $lines[] = ['account_id' => $purchasesAccount->id, 'debit' => $expenseAmount, 'credit' => 0, 'description' => 'Purchase expense'];
        }

        // Debit: Tax
        if ($taxAmount > 0) {
            $lines[] = ['account_id' => $taxAccount->id, 'debit' => $taxAmount, 'credit' => 0, 'description' => 'Input tax'];
        }

        // Credit: Accounts Payable
        $lines[] = ['account_id' => $apAccount->id, 'debit' => 0, 'credit' => $totalAmount, 'description' => 'Amount owed to supplier'];

        return $this->postEntry([
            'user_id' => $userId,
            'date' => now()->toDateString(),
            'description' => 'Purchase receipt',
            'source_type' => 'purchase',
            'source_id' => $sourceId,
        ], $lines);
    }

    /**
     * Post a purchase payment: Debit AP, Credit Cash.
     */
    public function postPurchasePayment(float $amount, string $sourceId): JournalEntry
    {
        $userId = Auth::id();
        $apAccount = $this->getSystemAccount($userId, '2000');
        $cashAccount = $this->getSystemAccount($userId, '1000');

        return $this->postEntry([
            'user_id' => $userId,
            'date' => now()->toDateString(),
            'description' => 'Purchase payment',
            'source_type' => 'purchase_payment',
            'source_id' => $sourceId,
        ], [
            ['account_id' => $apAccount->id, 'debit' => $amount, 'credit' => 0, 'description' => 'Payment to supplier'],
            ['account_id' => $cashAccount->id, 'debit' => 0, 'credit' => $amount, 'description' => 'Cash paid'],
        ]);
    }

    /**
     * Post an invoice payment: Debit Cash, Credit Accounts Receivable.
     */
    public function postInvoicePayment(float $amount, string $sourceId): JournalEntry
    {
        $userId = Auth::id();
        $cashAccount = $this->getSystemAccount($userId, '1000');
        $arAccount = $this->getSystemAccount($userId, '1100');

        return $this->postEntry([
            'user_id' => $userId,
            'date' => now()->toDateString(),
            'description' => 'Invoice payment received',
            'source_type' => 'invoice_payment',
            'source_id' => $sourceId,
        ], [
            ['account_id' => $cashAccount->id, 'debit' => $amount, 'credit' => 0, 'description' => 'Payment received'],
            ['account_id' => $arAccount->id, 'debit' => 0, 'credit' => $amount, 'description' => 'Receivable cleared'],
        ]);
    }

    /**
     * Post an expense: Debit Expense, Credit Cash/AP.
     */
    public function postExpense(float $amount, string $expenseCategory, bool $isPaid, string $sourceId): JournalEntry
    {
        $userId = Auth::id();
        $cashAccount = $this->getSystemAccount($userId, '1000');
        $apAccount = $this->getSystemAccount($userId, '2000');

        // Try to find a matching expense account by category
        $expenseAccount = Account::where('user_id', $userId)
            ->where('type', 'expense')
            ->where(function ($q) use ($expenseCategory) {
                $q->where('name', 'like', "%{$expenseCategory}%")
                  ->orWhere('subtype', 'like', "%{$expenseCategory}%");
            })
            ->first();

        if (!$expenseAccount) {
            $expenseAccount = $this->getSystemAccount($userId, '5900'); // Miscellaneous Expense
        }

        $lines = [
            ['account_id' => $expenseAccount->id, 'debit' => $amount, 'credit' => 0, 'description' => 'Expense: ' . $expenseCategory],
        ];

        if ($isPaid) {
            $lines[] = ['account_id' => $cashAccount->id, 'debit' => 0, 'credit' => $amount, 'description' => 'Cash paid'];
        } else {
            $lines[] = ['account_id' => $apAccount->id, 'debit' => 0, 'credit' => $amount, 'description' => 'Amount owed'];
        }

        return $this->postEntry([
            'user_id' => $userId,
            'date' => now()->toDateString(),
            'description' => 'Expense: ' . $expenseCategory,
            'source_type' => 'expense',
            'source_id' => $sourceId,
        ], $lines);
    }

    /**
     * Get a system account by code.
     */
    public function getSystemAccount(int $userId, string $code): Account
    {
        $account = Account::where('user_id', $userId)->where('code', $code)->first();

        if (!$account) {
            // Seed accounts if not found
            \Database\Seeders\ChartOfAccountsSeeder::seedForUser($userId);
            $account = Account::where('user_id', $userId)->where('code', $code)->first();
        }

        return $account;
    }

    /**
     * Generate Trial Balance data.
     */
    public function getTrialBalance(int $userId, ?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ? Carbon::parse($asOfDate) : now();

        $accounts = Account::where('user_id', $userId)
            ->with(['journalEntryLines' => function ($q) use ($asOfDate) {
                $q->whereHas('journalEntry', function ($jq) use ($asOfDate) {
                    $jq->where('is_posted', true)->where('date', '<=', $asOfDate);
                });
            }])
            ->active()
            ->orderBy('code')
            ->get();

        $totalDebit = 0;
        $totalCredit = 0;

        $trialBalance = $accounts->map(function ($account) use (&$totalDebit, &$totalCredit) {
            $debitTotal = $account->journalEntryLines->sum('debit');
            $creditTotal = $account->journalEntryLines->sum('credit');

            $balance = 0;
            $isDebit = false;

            if ($account->normal_balance === 'debit') {
                $balance = $debitTotal - $creditTotal;
                if ($balance > 0) {
                    $totalDebit += abs($balance);
                    $isDebit = true;
                } elseif ($balance < 0) {
                    $totalCredit += abs($balance);
                }
            } else {
                $balance = $creditTotal - $debitTotal;
                if ($balance > 0) {
                    $totalCredit += abs($balance);
                } elseif ($balance < 0) {
                    $totalDebit += abs($balance);
                    $isDebit = true;
                }
            }

            return [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'debit' => $isDebit ? abs($balance) : 0,
                'credit' => !$isDebit && $balance > 0 ? abs($balance) : ($balance < 0 && !$isDebit ? abs($balance) : 0),
            ];
        })->filter(fn($item) => $item['debit'] > 0 || $item['credit'] > 0)->values();

        return [
            'accounts' => $trialBalance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'as_of_date' => $asOfDate,
        ];
    }

    /**
     * Generate Income Statement data.
     */
    public function getIncomeStatement(int $userId, string $fromDate, string $toDate): array
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        $incomeAccounts = Account::where('user_id', $userId)
            ->where('type', 'income')
            ->with(['journalEntryLines' => function ($q) use ($from, $to) {
                $q->whereHas('journalEntry', function ($jq) use ($from, $to) {
                    $jq->where('is_posted', true)->whereBetween('date', [$from, $to]);
                });
            }])
            ->orderBy('code')
            ->get();

        $expenseAccounts = Account::where('user_id', $userId)
            ->where('type', 'expense')
            ->with(['journalEntryLines' => function ($q) use ($from, $to) {
                $q->whereHas('journalEntry', function ($jq) use ($from, $to) {
                    $jq->where('is_posted', true)->whereBetween('date', [$from, $to]);
                });
            }])
            ->orderBy('code')
            ->get();

        $totalRevenue = 0;
        $revenueItems = $incomeAccounts->map(function ($account) use (&$totalRevenue) {
            $creditTotal = $account->journalEntryLines->sum('credit');
            $debitTotal = $account->journalEntryLines->sum('debit');
            $balance = $creditTotal - $debitTotal;
            $totalRevenue += $balance;
            return [
                'code' => $account->code,
                'name' => $account->name,
                'balance' => $balance,
            ];
        })->filter(fn($item) => $item['balance'] != 0)->values();

        $totalExpenses = 0;
        $expenseItems = $expenseAccounts->map(function ($account) use (&$totalExpenses) {
            $debitTotal = $account->journalEntryLines->sum('debit');
            $creditTotal = $account->journalEntryLines->sum('credit');
            $balance = $debitTotal - $creditTotal;
            $totalExpenses += $balance;
            return [
                'code' => $account->code,
                'name' => $account->name,
                'balance' => $balance,
            ];
        })->filter(fn($item) => $item['balance'] != 0)->values();

        $cogsAmount = $expenseItems->where('code', '5000')->sum('balance');
        $grossProfit = $totalRevenue - $cogsAmount;
        $operatingExpenses = $totalExpenses - $cogsAmount;
        $netIncome = $totalRevenue - $totalExpenses;

        return [
            'revenue' => $revenueItems,
            'expenses' => $expenseItems,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'cogs' => $cogsAmount,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'net_income' => $netIncome,
            'from_date' => $from,
            'to_date' => $to,
        ];
    }

    /**
     * Generate Balance Sheet data.
     */
    public function getBalanceSheet(int $userId, ?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ? Carbon::parse($asOfDate) : now();

        $types = ['asset', 'liability', 'equity'];
        $result = [];

        foreach ($types as $type) {
            $accounts = Account::where('user_id', $userId)
                ->where('type', $type)
                ->with(['journalEntryLines' => function ($q) use ($asOfDate) {
                    $q->whereHas('journalEntry', function ($jq) use ($asOfDate) {
                        $jq->where('is_posted', true)->where('date', '<=', $asOfDate);
                    });
                }])
                ->orderBy('code')
                ->get();

            $typeTotal = 0;
            $items = $accounts->map(function ($account) use (&$typeTotal) {
                $debitTotal = $account->journalEntryLines->sum('debit');
                $creditTotal = $account->journalEntryLines->sum('credit');

                if ($account->normal_balance === 'debit') {
                    $balance = $debitTotal - $creditTotal;
                } else {
                    $balance = $creditTotal - $debitTotal;
                }

                $typeTotal += $balance;
                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $balance,
                ];
            })->filter(fn($item) => $item['balance'] != 0)->values();

            $result[$type] = [
                'items' => $items,
                'total' => $typeTotal,
            ];
        }

        // Add net income to equity
        $netIncome = ($result['income']['total'] ?? 0) - ($result['expenses']['total'] ?? 0);

        return [
            'assets' => $result['asset']['items'] ?? collect(),
            'total_assets' => $result['asset']['total'] ?? 0,
            'liabilities' => $result['liability']['items'] ?? collect(),
            'total_liabilities' => $result['liability']['total'] ?? 0,
            'equity' => $result['equity']['items'] ?? collect(),
            'total_equity' => ($result['equity']['total'] ?? 0),
            'net_income' => $netIncome,
            'total_liabilities_equity' => ($result['liability']['total'] ?? 0) + ($result['equity']['total'] ?? 0) + $netIncome,
            'as_of_date' => $asOfDate,
        ];
    }

    /**
     * Generate General Ledger for a specific account.
     */
    public function getGeneralLedger(int $userId, int $accountId, string $fromDate, string $toDate): array
    {
        $account = Account::where('user_id', $userId)->where('id', $accountId)->firstOrFail();

        $lines = JournalEntryLine::where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($userId, $fromDate, $toDate) {
                $q->where('user_id', $userId)
                  ->where('is_posted', true)
                  ->whereBetween('date', [$fromDate, $toDate]);
            })
            ->with('journalEntry')
            ->orderBy('created_at')
            ->get();

        $runningBalance = $account->opening_balance;
        $entries = $lines->map(function ($line) use (&$runningBalance, $account) {
            if ($account->normal_balance === 'debit') {
                $runningBalance += ($line->debit - $line->credit);
            } else {
                $runningBalance += ($line->credit - $line->debit);
            }

            return [
                'date' => $line->journalEntry->date,
                'reference' => $line->journalEntry->reference_number,
                'description' => $line->journalEntry->description,
                'debit' => $line->debit,
                'credit' => $line->credit,
                'balance' => $runningBalance,
            ];
        });

        return [
            'account' => $account,
            'entries' => $entries,
            'opening_balance' => $account->opening_balance,
            'closing_balance' => $runningBalance,
            'from_date' => Carbon::parse($fromDate),
            'to_date' => Carbon::parse($toDate),
        ];
    }
}
