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

// Invoice Routes
Route::middleware('auth')->group(function () {
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
    
    // Additional invoice actions
    Route::post('invoices/{invoice}/duplicate', [App\Http\Controllers\InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::post('invoices/{invoice}/mark-sent', [App\Http\Controllers\InvoiceController::class, 'markAsSent'])->name('invoices.mark-sent');
    Route::post('invoices/{invoice}/add-payment', [App\Http\Controllers\InvoiceController::class, 'addPayment'])->name('invoices.add-payment');
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

Route::get('/home', function() {
    return redirect('/dashboard');
})->name('home');

// Handle direct access to registration page
Route::get('/register', function() {
    return redirect()->route('login');
})->name('register');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
