<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // If user is authenticated, redirect to dashboard
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    // If not authenticated, redirect to login
    return redirect('/login');
});

Auth::routes(['register' => false]);

// Client Routes
Route::resource('clients', App\Http\Controllers\ClientController::class)->middleware('auth');

// Item Routes
Route::resource('items', App\Http\Controllers\ItemController::class)->middleware('auth');

// Expense Routes
Route::middleware('auth')->group(function () {
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
    
    // Additional expense actions
    Route::post('expenses/{expense}/duplicate', [App\Http\Controllers\ExpenseController::class, 'duplicate'])->name('expenses.duplicate');
    Route::post('expenses/{expense}/approve', [App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject', [App\Http\Controllers\ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::post('expenses/{expense}/mark-paid', [App\Http\Controllers\ExpenseController::class, 'markAsPaid'])->name('expenses.mark-paid');
    Route::delete('expenses/{expense}/attachments', [App\Http\Controllers\ExpenseController::class, 'deleteAttachment'])->name('expenses.delete-attachment');
});

// Invoice Routes
Route::middleware('auth')->group(function () {
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
    
    // Additional invoice actions
    Route::post('invoices/{invoice}/duplicate', [App\Http\Controllers\InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::post('invoices/{invoice}/mark-sent', [App\Http\Controllers\InvoiceController::class, 'markAsSent'])->name('invoices.mark-sent');
    Route::post('invoices/{invoice}/add-payment', [App\Http\Controllers\InvoiceController::class, 'addPayment'])->name('invoices.add-payment');
    Route::post('invoices/{invoice}/recalculate-payments', [App\Http\Controllers\InvoiceController::class, 'recalculatePayments'])->name('invoices.recalculate-payments');
    Route::post('invoices/{invoice}/send-email', [App\Http\Controllers\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/preview', [App\Http\Controllers\InvoiceController::class, 'preview'])->name('invoices.preview');
    Route::post('invoices/preview', [App\Http\Controllers\InvoiceController::class, 'previewFromForm'])->name('invoices.preview-form');
});

// Quotation Routes
Route::middleware('auth')->group(function () {
    Route::resource('quotations', App\Http\Controllers\QuotationController::class);
    
    // Additional quotation actions
    Route::post('quotations/{quotation}/accept', [App\Http\Controllers\QuotationController::class, 'accept'])->name('quotations.accept');
    Route::post('quotations/{quotation}/reject', [App\Http\Controllers\QuotationController::class, 'reject'])->name('quotations.reject');
    Route::post('quotations/{quotation}/convert-to-invoice', [App\Http\Controllers\QuotationController::class, 'convertToInvoice'])->name('quotations.convert-to-invoice');
    Route::post('quotations/{quotation}/send-email', [App\Http\Controllers\QuotationController::class, 'sendEmail'])->name('quotations.send-email');
    Route::get('quotations/{quotation}/pdf', [App\Http\Controllers\QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::get('quotations/{quotation}/preview', [App\Http\Controllers\QuotationController::class, 'preview'])->name('quotations.preview');
});

// Projects Routes
Route::middleware('auth')->group(function () {
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    Route::get('projects-dashboard', [App\Http\Controllers\ProjectController::class, 'dashboard'])->name('projects.dashboard');
    Route::post('projects/{project}/tasks', [App\Http\Controllers\ProjectTaskController::class, 'store'])->name('projects.tasks.store');
    Route::put('projects/{project}/tasks/{task}', [App\Http\Controllers\ProjectTaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('projects/{project}/tasks/{task}', [App\Http\Controllers\ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy');
    
    // Debug route for testing task operations
    Route::get('debug/task/{task}', function(\App\Models\ProjectTask $task) {
        return response()->json([
            'id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
            'priority' => $task->priority,
            'progress' => $task->percent_complete
        ]);
    })->name('debug.task');
});

// Settings Routes
Route::middleware('auth')->group(function () {
    Route::get('settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    Route::delete('settings/logo', [App\Http\Controllers\SettingsController::class, 'removeLogo'])->name('settings.remove-logo');
});

// Reports Routes
Route::middleware('auth')->group(function () {
    Route::resource('reports', App\Http\Controllers\ReportController::class);
    
    // Additional report actions
    Route::post('reports/{report}/generate', [App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{report}/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('reports/{report}/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('reports/{report}/export/csv', [App\Http\Controllers\ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::post('reports/{report}/duplicate', [App\Http\Controllers\ReportController::class, 'duplicate'])->name('reports.duplicate');
    
    // Reports Dashboard and Testing
    Route::get('reports-dashboard', [App\Http\Controllers\ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::post('reports/{report}/test-scheduled', [App\Http\Controllers\ReportController::class, 'testScheduled'])->name('reports.test-scheduled');
    
    // Quick Reports - Direct access without configuration
    Route::get('reports/quick/revenue', [App\Http\Controllers\ReportController::class, 'quickRevenue'])->name('reports.quick.revenue');
    Route::get('reports/quick/client', [App\Http\Controllers\ReportController::class, 'quickClient'])->name('reports.quick.client');
    Route::get('reports/quick/monthly', [App\Http\Controllers\ReportController::class, 'quickMonthly'])->name('reports.quick.monthly');
    Route::get('reports/quick/yearly', [App\Http\Controllers\ReportController::class, 'quickYearly'])->name('reports.quick.yearly');
    Route::get('reports/quick/product-performance', [App\Http\Controllers\ReportController::class, 'quickProductPerformance'])->name('reports.quick.product-performance');
    Route::get('reports/quick/overdue-invoices', [App\Http\Controllers\ReportController::class, 'quickOverdueInvoices'])->name('reports.quick.overdue-invoices');
    Route::get('reports/quick/cash-flow', [App\Http\Controllers\ReportController::class, 'quickCashFlow'])->name('reports.quick.cash-flow');
    Route::get('reports/quick/profit-loss', [App\Http\Controllers\ReportController::class, 'quickProfitLoss'])->name('reports.quick.profit-loss');
    Route::get('reports/quick/top-clients', [App\Http\Controllers\ReportController::class, 'quickTopClients'])->name('reports.quick.top-clients');
    Route::get('reports/quick/growth-analysis', [App\Http\Controllers\ReportController::class, 'quickGrowthAnalysis'])->name('reports.quick.growth-analysis');
    Route::get('reports/quick/payment-methods', [App\Http\Controllers\ReportController::class, 'quickPaymentMethods'])->name('reports.quick.payment-methods');
    Route::get('reports/quick/seasonal-trends', [App\Http\Controllers\ReportController::class, 'quickSeasonalTrends'])->name('reports.quick.seasonal-trends');
    Route::get('reports/quick/payment-status', [App\Http\Controllers\ReportController::class, 'quickPaymentStatus'])->name('reports.quick.payment-status');
    Route::get('reports/quick/outstanding', [App\Http\Controllers\ReportController::class, 'quickOutstanding'])->name('reports.quick.outstanding');
    
    // Quick Report Exports
    Route::get('reports/quick/export/pdf', [App\Http\Controllers\ReportController::class, 'exportQuickPdf'])->name('reports.quick.export.pdf');
    Route::get('reports/quick/export/excel', [App\Http\Controllers\ReportController::class, 'exportQuickExcel'])->name('reports.quick.export.excel');
});

// Domain Management Routes
Route::middleware('auth')->group(function () {
    Route::resource('domains', App\Http\Controllers\DomainController::class);
    
    // Additional domain actions
    Route::post('domains/check-availability', [App\Http\Controllers\DomainController::class, 'checkAvailability'])->name('domains.check-availability');
    Route::post('domains/register', [App\Http\Controllers\DomainController::class, 'register'])->name('domains.register');
    Route::post('domains/{domain}/renew', [App\Http\Controllers\DomainController::class, 'renew'])->name('domains.renew');
    Route::post('domains/{domain}/update-nameservers', [App\Http\Controllers\DomainController::class, 'updateNameservers'])->name('domains.update-nameservers');
    Route::post('domains/{domain}/sync', [App\Http\Controllers\DomainController::class, 'sync'])->name('domains.sync');
    Route::get('domains/get-pricing', [App\Http\Controllers\DomainController::class, 'getPricing'])->name('domains.get-pricing');
    
    // Sync domains from NameSilo account
    Route::post('domains/sync-from-account', [App\Http\Controllers\DomainController::class, 'syncFromAccount'])->name('domains.sync-from-account');
    Route::get('domains/list-from-account', [App\Http\Controllers\DomainController::class, 'listFromAccount'])->name('domains.list-from-account');
    
    // Debug route
    Route::get('domains/{domain}/debug', function(\App\Models\Domain $domain) {
        if ($domain->user_id !== Auth::id()) {
            abort(403);
        }
        return response()->json([
            'domain_name' => $domain->domain_name,
            'nameservers' => $domain->nameservers,
            'nameservers_type' => gettype($domain->nameservers),
            'dns_records' => $domain->dns_records,
            'dns_records_type' => gettype($domain->dns_records),
            'api_data' => $domain->api_data,
            'api_data_type' => gettype($domain->api_data),
            'notes' => $domain->notes,
            'notes_type' => gettype($domain->notes),
        ]);
    })->name('domains.debug');
});

// Email Testing Routes
Route::middleware('auth')->group(function () {
    Route::get('test-email/invoice/{invoice}', function(\App\Models\Invoice $invoice) {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        
        $service = app(\App\Services\SimpleEmailService::class);
        $success = $service->sendInvoice($invoice, 'test@example.com', 'Test Invoice Email', 'This is a test email message.');
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Test invoice email sent to mycosoftt@gmail.com!' : 'Failed to send test email'
        ]);
    })->name('test.email.invoice');

    Route::get('test-email/quotation/{quotation}', function(\App\Models\Quotation $quotation) {
        if ($quotation->user_id !== auth()->id()) {
            abort(403);
        }
        
        $service = app(\App\Services\SimpleEmailService::class);
        $success = $service->sendQuotation($quotation, 'test@example.com', 'Test Quotation Email', 'This is a test email message.');
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Test quotation email sent to mycosoftt@gmail.com!' : 'Failed to send test email'
        ]);
    })->name('test.email.quotation');

    // Receipt PDF route
    Route::get('receipts/{payment}/pdf', function(\App\Models\InvoicePayment $payment) {
        if ($payment->invoice->user_id !== auth()->id()) {
            abort(403);
        }
        
        $settings = \App\Models\Setting::forUser($payment->invoice->user_id);
        
        // Helper function for number to words
        function numberToWords($number) {
            $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
            $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
            $thousands = ['', 'thousand', 'million', 'billion'];
            
            if ($number == 0) return 'zero';
            
            $num = (int)$number;
            $result = '';
            
            if ($num >= 1000000) {
                $millions = (int)($num / 1000000);
                $result .= numberToWords($millions) . ' million ';
                $num %= 1000000;
            }
            
            if ($num >= 1000) {
                $thousands_num = (int)($num / 1000);
                $result .= numberToWords($thousands_num) . ' thousand ';
                $num %= 1000;
            }
            
            if ($num >= 100) {
                $hundreds = (int)($num / 100);
                $result .= $ones[$hundreds] . ' hundred ';
                $num %= 100;
            }
            
            if ($num >= 20) {
                $tens_digit = (int)($num / 10);
                $result .= $tens[$tens_digit] . ' ';
                $num %= 10;
            }
            
            if ($num > 0) {
                $result .= $ones[$num] . ' ';
            }
            
            return trim($result);
        }
        
        $pdf = app('dompdf.wrapper')->loadView('receipts.pdf', [
            'payment' => $payment,
            'invoice' => $payment->invoice,
            'settings' => $settings
        ]);
        
        return $pdf->download("receipt-{$payment->id}.pdf");
    })->name('receipts.pdf');
});

Route::get('/home', function() {
    return redirect('/dashboard');
})->name('home');

// Handle direct access to registration page
Route::get('/register', function() {
    return redirect()->route('login');
})->name('register');
// ============================================
// NEW MODULES: POS, Purchases, Inventory, Accounts
// ============================================

// Account Routes
Route::middleware('auth')->group(function () {
    Route::resource('accounts', \App\Http\Controllers\AccountController::class);
    Route::post('accounts/initialize', [\App\Http\Controllers\AccountController::class, 'initialize'])->name('accounts.initialize');
});

// Journal Entry Routes
Route::middleware('auth')->group(function () {
    Route::resource('journal-entries', \App\Http\Controllers\JournalEntryController::class);
    Route::post('journal-entries/{journalEntry}/reverse', [\App\Http\Controllers\JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');
});

// Supplier Routes
Route::middleware('auth')->group(function () {
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
});

// Purchase Routes
Route::middleware('auth')->group(function () {
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
    Route::post('purchases/{purchase}/receive', [\App\Http\Controllers\PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('purchases/{purchase}/add-payment', [\App\Http\Controllers\PurchaseController::class, 'addPayment'])->name('purchases.add-payment');
});

// Inventory Routes
Route::middleware('auth')->group(function () {
    Route::get('inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/adjustments', [\App\Http\Controllers\InventoryController::class, 'adjustments'])->name('inventory.adjustments');
    Route::post('inventory/adjustments', [\App\Http\Controllers\InventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');
    Route::get('inventory/movements', [\App\Http\Controllers\InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('inventory/low-stock', [\App\Http\Controllers\InventoryController::class, 'lowStock'])->name('inventory.low-stock');
});

// POS Routes
Route::middleware('auth')->group(function () {
    Route::get('pos/terminal', [\App\Http\Controllers\PosController::class, 'terminal'])->name('pos.terminal');
    Route::post('pos/store', [\App\Http\Controllers\PosController::class, 'store'])->name('pos.store');
    Route::get('pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::get('pos/{posSale}', [\App\Http\Controllers\PosController::class, 'show'])->name('pos.show');
    Route::get('pos/{posSale}/receipt', [\App\Http\Controllers\PosController::class, 'receipt'])->name('pos.receipt');
    Route::post('pos/{posSale}/link-to-invoice', [\App\Http\Controllers\PosController::class, 'linkToInvoice'])->name('pos.link-to-invoice');
    Route::get('pos-daily-summary', [\App\Http\Controllers\PosController::class, 'dailySummary'])->name('pos.daily-summary');
    Route::get('pos/search-items', [\App\Http\Controllers\PosController::class, 'searchItems'])->name('pos.search-items');
});

// Financial Reports Routes
Route::middleware('auth')->prefix('financial-reports')->name('financial-reports.')->group(function () {
    Route::get('trial-balance', [\App\Http\Controllers\FinancialReportController::class, 'trialBalance'])->name('trial-balance');
    Route::get('income-statement', [\App\Http\Controllers\FinancialReportController::class, 'incomeStatement'])->name('income-statement');
    Route::get('balance-sheet', [\App\Http\Controllers\FinancialReportController::class, 'balanceSheet'])->name('balance-sheet');
    Route::get('cash-flow', [\App\Http\Controllers\FinancialReportController::class, 'cashFlow'])->name('cash-flow');
    Route::get('general-ledger', [\App\Http\Controllers\FinancialReportController::class, 'generalLedger'])->name('general-ledger');
    Route::get('journal-report', [\App\Http\Controllers\FinancialReportController::class, 'journalReport'])->name('journal-report');
    Route::get('export/{report}', [\App\Http\Controllers\FinancialReportController::class, 'exportPdf'])->name('export-pdf');
});

Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

// ============================================
// USER MANAGEMENT & ACTIVITY LOGS
// ============================================

// User Management Routes
Route::middleware(['auth', 'can:view-users'])->group(function () {
    Route::resource('users', \App\Http\Controllers\UserManagementController::class);
});

// Activity Logs
Route::middleware(['auth', 'can:view-activity-logs'])->group(function () {
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
});

