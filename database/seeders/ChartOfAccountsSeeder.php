<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $this->seedForUser($user->id);
        }
    }

    public static function seedForUser(int $userId): void
    {
        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'subtype' => 'current', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1010', 'name' => 'Petty Cash', 'type' => 'asset', 'subtype' => 'current', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'subtype' => 'current', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset', 'subtype' => 'current', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'subtype' => 'current', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '1500', 'name' => 'Fixed Assets', 'type' => 'asset', 'subtype' => 'non_current', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '1501', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'subtype' => 'non_current', 'normal_balance' => 'credit', 'is_system' => false],

            // Liabilities
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'subtype' => 'current', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '2100', 'name' => 'Tax Payable', 'type' => 'liability', 'subtype' => 'current', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '2200', 'name' => 'Accrued Expenses', 'type' => 'liability', 'subtype' => 'current', 'normal_balance' => 'credit', 'is_system' => false],
            ['code' => '2300', 'name' => 'Customer Deposits', 'type' => 'liability', 'subtype' => 'current', 'normal_balance' => 'credit', 'is_system' => false],
            ['code' => '2500', 'name' => 'Long-term Liabilities', 'type' => 'liability', 'subtype' => 'non_current', 'normal_balance' => 'credit', 'is_system' => false],

            // Equity
            ['code' => '3000', 'name' => "Owner's Equity", 'type' => 'equity', 'subtype' => null, 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'subtype' => null, 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '3200', 'name' => "Owner's Drawings", 'type' => 'equity', 'subtype' => null, 'normal_balance' => 'debit', 'is_system' => false],

            // Income
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'income', 'subtype' => 'operating', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'income', 'subtype' => 'operating', 'normal_balance' => 'credit', 'is_system' => true],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'income', 'subtype' => 'other', 'normal_balance' => 'credit', 'is_system' => false],
            ['code' => '4300', 'name' => 'Sales Discounts', 'type' => 'income', 'subtype' => 'contra', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '4400', 'name' => 'Sales Returns and Allowances', 'type' => 'income', 'subtype' => 'contra', 'normal_balance' => 'debit', 'is_system' => false],

            // Expenses
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'subtype' => 'cost_of_sales', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '5100', 'name' => 'Purchases', 'type' => 'expense', 'subtype' => 'cost_of_sales', 'normal_balance' => 'debit', 'is_system' => true],
            ['code' => '5200', 'name' => 'Rent Expense', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5300', 'name' => 'Utilities Expense', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5400', 'name' => 'Salaries and Wages', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5500', 'name' => 'Office Supplies', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5600', 'name' => 'Marketing Expense', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5700', 'name' => 'Depreciation Expense', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5800', 'name' => 'Bank Charges', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
            ['code' => '5900', 'name' => 'Miscellaneous Expense', 'type' => 'expense', 'subtype' => 'operating', 'normal_balance' => 'debit', 'is_system' => false],
        ];

        foreach ($accounts as $account) {
            Account::create(array_merge($account, [
                'user_id' => $userId,
                'status' => 'active',
                'opening_balance' => 0,
                'current_balance' => 0,
            ]));
        }
    }
}
