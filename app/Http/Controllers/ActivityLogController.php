<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display activity logs with filtering.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-activity-logs')) {
            abort(403);
        }

        $query = Activity::with('causer')->latest();

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('causer_type', User::class)
                  ->where('causer_id', $request->user_id);
        }

        // Filter by model type
        if ($request->has('model_type') && $request->model_type) {
            $query->where('subject_type', 'App\\Models\\' . $request->model_type);
        }

        // Filter by action/event
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(30)->appends($request->all());
        $users = User::orderBy('name')->get();
        $events = ['created', 'updated', 'deleted'];
        $models = [
            'Invoice' => 'Invoices',
            'InvoicePayment' => 'Invoice Payments',
            'Client' => 'Clients',
            'Item' => 'Items',
            'Quotation' => 'Quotations',
            'Expense' => 'Expenses',
            'Project' => 'Projects',
            'ProjectTask' => 'Project Tasks',
            'Domain' => 'Domains',
            'PosSale' => 'POS Sales',
            'Purchase' => 'Purchases',
            'Supplier' => 'Suppliers',
            'Account' => 'Accounts',
            'JournalEntry' => 'Journal Entries',
            'Setting' => 'Settings',
            'User' => 'Users',
            'StockMovement' => 'Stock Movements',
        ];

        return view('activity-logs.index', compact('activities', 'users', 'events', 'models'));
    }
}
