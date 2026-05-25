<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // Define all permissions grouped by module
        // ==========================================
        $permissions = [
            // Users Management
            'view-users', 'create-users', 'edit-users', 'delete-users', 'assign-roles',

            // Roles & Permissions
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',

            // Clients
            'view-clients', 'create-clients', 'edit-clients', 'delete-clients',

            // Items / Products
            'view-items', 'create-items', 'edit-items', 'delete-items',

            // Invoices
            'view-invoices', 'create-invoices', 'edit-invoices', 'delete-invoices',
            'send-invoices', 'add-payments', 'download-invoice-pdf',

            // Quotations
            'view-quotations', 'create-quotations', 'edit-quotations', 'delete-quotations',
            'convert-to-invoice', 'send-quotations', 'download-quotation-pdf',

            // Expenses
            'view-expenses', 'create-expenses', 'edit-expenses', 'delete-expenses',
            'approve-expenses', 'mark-expenses-paid',

            // Projects
            'view-projects', 'create-projects', 'edit-projects', 'delete-projects',
            'manage-tasks',

            // Reports
            'view-reports', 'create-reports', 'delete-reports',
            'export-reports', 'view-report-dashboard',
            'view-revenue-report', 'view-client-report', 'view-monthly-report',
            'view-yearly-report', 'view-cash-flow-report', 'view-profit-loss',
            'view-top-clients', 'view-overdue-report', 'view-payment-status',
            'view-outstanding-report', 'view-product-performance',

            // Domains
            'view-domains', 'create-domains', 'edit-domains', 'delete-domains',
            'manage-dns', 'sync-domains', 'register-domains',

            // POS
            'access-pos-terminal', 'view-pos-sales', 'view-daily-pos-summary',

            // Purchases
            'view-purchases', 'create-purchases', 'edit-purchases', 'delete-purchases',
            'receive-purchases', 'add-purchase-payments',

            // Suppliers
            'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',

            // Inventory
            'view-inventory', 'adjust-inventory',
            'view-stock-movements', 'view-low-stock',

            // Accounting - Chart of Accounts
            'view-accounts', 'create-accounts', 'edit-accounts', 'delete-accounts',

            // Accounting - Journal Entries
            'view-journal-entries', 'create-journal-entries', 'edit-journal-entries',
            'delete-journal-entries', 'reverse-journal-entries',

            // Financial Reports
            'view-trial-balance', 'view-income-statement', 'view-balance-sheet',
            'view-financial-cash-flow', 'view-general-ledger', 'view-journal-report',

            // Settings
            'access-settings',

            // Activity Logs
            'view-activity-logs',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('Created ' . count($permissions) . ' permissions.');

        // ==========================================
        // Create Roles
        // ==========================================

        // 1. Super Admin - All permissions
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - Almost all except critical system perms
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all()->reject(function ($p) {
            return in_array($p->name, ['delete-users', 'delete-roles', 'assign-roles',
                'view-activity-logs', 'delete-invoices', 'delete-quotations', 'delete-journal-entries',
                'reverse-journal-entries', 'delete-accounts', 'delete-purchases', 'delete-expenses']);
        }));

        // 3. Manager - Daily operations
        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view-clients', 'create-clients', 'edit-clients',
            'view-items', 'create-items', 'edit-items', 'delete-items',
            'view-invoices', 'create-invoices', 'edit-invoices', 'send-invoices',
            'add-payments', 'download-invoice-pdf',
            'view-quotations', 'create-quotations', 'edit-quotations',
            'convert-to-invoice', 'send-quotations', 'download-quotation-pdf',
            'view-expenses', 'create-expenses', 'edit-expenses', 'approve-expenses', 'mark-expenses-paid',
            'view-projects', 'create-projects', 'edit-projects', 'manage-tasks',
            'view-purchases', 'create-purchases', 'edit-purchases', 'receive-purchases', 'add-purchase-payments',
            'view-suppliers', 'create-suppliers', 'edit-suppliers',
            'view-inventory', 'adjust-inventory', 'view-stock-movements', 'view-low-stock',
            'access-pos-terminal', 'view-pos-sales', 'view-daily-pos-summary',
            // Reports
            'view-reports', 'create-reports', 'export-reports', 'view-report-dashboard',
            'view-revenue-report', 'view-client-report', 'view-monthly-report',
            'view-yearly-report', 'view-cash-flow-report', 'view-profit-loss',
            'view-top-clients', 'view-overdue-report', 'view-payment-status',
            'view-outstanding-report', 'view-product-performance',
        ]);

        // 4. Accountant - Finance focused
        $accountant = Role::create(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountant->givePermissionTo([
            'view-clients',
            'view-invoices', 'create-invoices', 'edit-invoices', 'send-invoices',
            'add-payments', 'download-invoice-pdf',
            'view-quotations', 'download-quotation-pdf',
            'view-expenses', 'create-expenses', 'edit-expenses', 'approve-expenses', 'mark-expenses-paid',
            'view-purchases', 'view-suppliers',
            'view-inventory', 'view-stock-movements',
            // Accounting
            'view-accounts', 'create-accounts', 'edit-accounts',
            'view-journal-entries', 'create-journal-entries', 'edit-journal-entries',
            'reverse-journal-entries',
            // Financial Reports
            'view-trial-balance', 'view-income-statement', 'view-balance-sheet',
            'view-financial-cash-flow', 'view-general-ledger', 'view-journal-report',
            // Reports
            'view-reports', 'create-reports', 'export-reports', 'view-report-dashboard',
            'view-revenue-report', 'view-client-report', 'view-monthly-report',
            'view-yearly-report', 'view-cash-flow-report', 'view-profit-loss',
            'view-top-clients', 'view-overdue-report', 'view-payment-status',
            'view-outstanding-report', 'view-product-performance',
        ]);

        // 5. Cashier - POS only
        $cashier = Role::create(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashier->givePermissionTo([
            'access-pos-terminal', 'view-pos-sales', 'view-daily-pos-summary',
            'view-items', 'view-clients',
        ]);

        // 6. Viewer - Read-only
        $viewer = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewer->givePermissionTo([
            'view-items', 'view-clients', 'view-invoices', 'view-quotations',
            'view-expenses', 'view-projects', 'view-purchases', 'view-suppliers',
            'view-pos-sales', 'view-inventory', 'view-stock-movements', 'view-low-stock',
            'view-reports', 'view-report-dashboard', 'view-revenue-report',
            'view-client-report', 'view-monthly-report', 'view-yearly-report',
            'view-cash-flow-report', 'view-profit-loss', 'view-top-clients',
            'view-overdue-report', 'view-payment-status', 'view-outstanding-report',
        ]);

        // ==========================================
        // Assign Super Admin to user ID 1
        // ==========================================
        $user = User::find(1);
        if ($user) {
            $user->assignRole($superAdmin);
            $this->command->info('Super Admin role assigned to user: ' . $user->email);
        }

        // ==========================================
        // Also assign to any admin@ users
        // ==========================================
        $adminUsers = User::where('email', 'like', 'admin@%')->where('id', '!=', 1)->get();
        foreach ($adminUsers as $u) {
            $u->assignRole($superAdmin);
            $this->command->info('Super Admin role assigned to: ' . $u->email);
        }
    }
}
