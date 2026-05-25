<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Client;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->expenses()->with(['client', 'project', 'approver']);
        
        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Handle status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Handle payment status filter
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Handle category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        // Handle client filter
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        
        // Handle project filter
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        
        // Handle date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
        
        // Handle overdue filter
        if ($request->has('overdue') && $request->overdue) {
            $query->overdue();
        }
        
        // Handle pending approval filter
        if ($request->has('pending_approval') && $request->pending_approval) {
            $query->pendingApproval();
        }
        
        // Handle billable filter
        if ($request->has('billable') && $request->billable) {
            $query->billable();
        }
        
        // Handle reimbursable filter
        if ($request->has('reimbursable') && $request->reimbursable) {
            $query->reimbursable();
        }
        
        $expenses = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get clients for filter dropdown
        $clients = Auth::user()->clients()->orderBy('name')->get();
        
        // Get projects for filter dropdown
        $projects = Auth::user()->projects()->orderBy('name')->get();
        
        // Get categories for filter dropdown
        $categories = Expense::getCategories();
        
        // Calculate summary statistics
        $stats = $this->getExpenseStats();
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('expenses.index', compact('expenses', 'clients', 'projects', 'categories', 'stats', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Auth::user()->clients()->orderBy('name')->get();
        $projects = Auth::user()->projects()->orderBy('name')->get();
        $categories = Expense::getCategories();
        $paymentMethods = Expense::getPaymentMethods();
        
        // Generate next expense number
        $expenseNumber = Expense::generateExpenseNumber(Auth::id());
        
        // Get user settings for currency and defaults
        $settings = Setting::forUser();
        
        return view('expenses.create', compact('clients', 'projects', 'categories', 'paymentMethods', 'expenseNumber', 'settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'expense_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expenses')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'reference_number' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_phone' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'expense_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:expense_date',
            'status' => 'required|in:draft,pending,approved,rejected,paid,cancelled',
            'payment_status' => 'required|in:unpaid,paid,partial',
            'payment_method' => 'nullable|in:cash,credit_card,debit_card,bank_transfer,check,paypal,other',
            'requires_approval' => 'boolean',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
            'is_billable' => 'boolean',
            'is_reimbursable' => 'boolean',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,true|in:monthly,quarterly,yearly',
            'recurring_end_date' => 'nullable|date|after:expense_date',
            'receipt_number' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['is_billable'] = $request->boolean('is_billable');
        $validated['is_reimbursable'] = $request->boolean('is_reimbursable');
        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['requires_approval'] = $request->boolean('requires_approval');
        
        // Verify client belongs to user if provided
        if ($validated['client_id']) {
            $client = Auth::user()->clients()->findOrFail($validated['client_id']);
        }
        
        // Verify project belongs to user if provided
        if ($validated['project_id']) {
            $project = Auth::user()->projects()->findOrFail($validated['project_id']);
        }
        
        DB::beginTransaction();
        
        try {
            // Handle file uploads
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('expenses/attachments', 'public');
                    $attachmentPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }
            $validated['attachments'] = $attachmentPaths;
            
            // Create expense
            $expense = Expense::create($validated);
            
            DB::commit();
            
            return redirect()->route('expenses.show', $expense)
                            ->with('success', 'Expense created successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create expense: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $expense->load(['client', 'approver']);
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('expenses.show', compact('expense', 'settings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $clients = Auth::user()->clients()->orderBy('name')->get();
        $projects = Auth::user()->projects()->orderBy('name')->get();
        $categories = Expense::getCategories();
        $paymentMethods = Expense::getPaymentMethods();
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('expenses.edit', compact('expense', 'clients', 'projects', 'categories', 'paymentMethods', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'expense_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expenses')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($expense->id)
            ],
            'reference_number' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_phone' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'expense_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:expense_date',
            'status' => 'required|in:draft,pending,approved,rejected,paid,cancelled',
            'payment_status' => 'required|in:unpaid,paid,partial',
            'payment_method' => 'nullable|in:cash,credit_card,debit_card,bank_transfer,check,paypal,other',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
            'is_billable' => 'boolean',
            'is_reimbursable' => 'boolean',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,true|in:monthly,quarterly,yearly',
            'recurring_end_date' => 'nullable|date|after:expense_date',
            'receipt_number' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
        ]);
        
        $validated['is_billable'] = $request->boolean('is_billable');
        $validated['is_reimbursable'] = $request->boolean('is_reimbursable');
        $validated['is_recurring'] = $request->boolean('is_recurring');
        
        // Verify client belongs to user if provided
        if ($validated['client_id']) {
            $client = Auth::user()->clients()->findOrFail($validated['client_id']);
        }
        
        // Verify project belongs to user if provided
        if ($validated['project_id']) {
            $project = Auth::user()->projects()->findOrFail($validated['project_id']);
        }
        
        DB::beginTransaction();
        
        try {
            // Handle file uploads
            $existingAttachments = $expense->attachments ?? [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('expenses/attachments', 'public');
                    $existingAttachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }
            $validated['attachments'] = $existingAttachments;
            
            // Update expense
            $expense->update($validated);
            
            DB::commit();
            
            return redirect()->route('expenses.show', $expense)
                            ->with('success', 'Expense updated successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update expense: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete associated attachments
            if ($expense->attachments) {
                foreach ($expense->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
            
            $expense->delete();
            
            DB::commit();
            
            return redirect()->route('expenses.index')
                            ->with('success', 'Expense deleted successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Duplicate an existing expense.
     */
    public function duplicate(Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        DB::beginTransaction();
        
        try {
            $newExpenseData = $expense->toArray();
            
            // Remove fields that should not be duplicated
            unset($newExpenseData['id']);
            unset($newExpenseData['expense_number']);
            unset($newExpenseData['paid_date']);
            unset($newExpenseData['approved_by']);
            unset($newExpenseData['approved_at']);
            unset($newExpenseData['approval_notes']);
            unset($newExpenseData['created_at']);
            unset($newExpenseData['updated_at']);
            
            // Reset status and dates
            $newExpenseData['status'] = 'draft';
            $newExpenseData['payment_status'] = 'unpaid';
            $newExpenseData['expense_date'] = now()->format('Y-m-d');
            $newExpenseData['due_date'] = $expense->due_date ? now()->addDays(30)->format('Y-m-d') : null;
            
            // Generate new expense number
            $newExpenseData['expense_number'] = Expense::generateExpenseNumber(Auth::id());
            
            $newExpense = Expense::create($newExpenseData);
            
            DB::commit();
            
            return redirect()->route('expenses.edit', $newExpense)
                            ->with('success', 'Expense duplicated successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to duplicate expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve an expense.
     */
    public function approve(Request $request, Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            $expense->approve(Auth::id(), $validated['approval_notes']);
            
            return back()->with('success', 'Expense approved successfully!');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to approve expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject an expense.
     */
    public function reject(Request $request, Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ]);
        
        try {
            $expense->reject(Auth::id(), $validated['approval_notes']);
            
            return back()->with('success', 'Expense rejected successfully!');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reject expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark expense as paid.
     */
    public function markAsPaid(Request $request, Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,check,paypal,other',
        ]);
        
        try {
            $expense->markAsPaid($validated['payment_method']);
            
            return back()->with('success', 'Expense marked as paid successfully!');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to mark expense as paid: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete an attachment.
     */
    public function deleteAttachment(Request $request, Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'attachment_index' => 'required|integer|min:0',
        ]);
        
        try {
            $attachments = $expense->attachments ?? [];
            $index = $validated['attachment_index'];
            
            if (isset($attachments[$index])) {
                // Delete file from storage
                Storage::disk('public')->delete($attachments[$index]['path']);
                
                // Remove from array
                unset($attachments[$index]);
                
                // Reindex array
                $attachments = array_values($attachments);
                
                // Update expense
                $expense->update(['attachments' => $attachments]);
                
                return back()->with('success', 'Attachment deleted successfully!');
            }
            
            return back()->withErrors(['error' => 'Attachment not found.']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete attachment: ' . $e->getMessage()]);
        }
    }

    /**
     * Get expense statistics.
     */
    private function getExpenseStats()
    {
        $userId = Auth::id();
        
        return [
            'total_count' => Auth::user()->expenses()->count(),
            'total_amount' => Auth::user()->expenses()->sum('amount'),
            'unpaid_count' => Auth::user()->expenses()->where('payment_status', 'unpaid')->count(),
            'unpaid_amount' => Auth::user()->expenses()->where('payment_status', 'unpaid')->sum('amount'),
            'paid_count' => Auth::user()->expenses()->where('payment_status', 'paid')->count(),
            'paid_amount' => Auth::user()->expenses()->where('payment_status', 'paid')->sum('amount'),
            'pending_approval_count' => Auth::user()->expenses()->pendingApproval()->count(),
            'pending_approval_amount' => Auth::user()->expenses()->pendingApproval()->sum('amount'),
            'overdue_count' => Auth::user()->expenses()->overdue()->count(),
            'overdue_amount' => Auth::user()->expenses()->overdue()->sum('amount'),
            'billable_count' => Auth::user()->expenses()->billable()->count(),
            'billable_amount' => Auth::user()->expenses()->billable()->sum('amount'),
            'reimbursable_count' => Auth::user()->expenses()->reimbursable()->count(),
            'reimbursable_amount' => Auth::user()->expenses()->reimbursable()->sum('amount'),
            'this_month_count' => Auth::user()->expenses()->whereMonth('expense_date', now()->month)->count(),
            'this_month_amount' => Auth::user()->expenses()->whereMonth('expense_date', now()->month)->sum('amount'),
            'this_year_count' => Auth::user()->expenses()->whereYear('expense_date', now()->year)->count(),
            'this_year_amount' => Auth::user()->expenses()->whereYear('expense_date', now()->year)->sum('amount'),
        ];
    }
}
