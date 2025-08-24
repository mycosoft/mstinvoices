<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\Item;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use PDF;

class InvoiceController extends Controller
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
        $query = Auth::user()->invoices()->with(['client', 'invoiceItems']);
        
        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
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
        
        // Handle client filter
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        
        // Handle date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }
        
        // Handle overdue filter
        if ($request->has('overdue') && $request->overdue) {
            $query->overdue();
        }
        
        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get clients for filter dropdown
        $clients = Auth::user()->clients()->active()->orderBy('name')->get();
        
        // Calculate summary statistics
        $stats = $this->getInvoiceStats();
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('invoices.index', compact('invoices', 'clients', 'stats', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Auth::user()->clients()->active()->orderBy('name')->get();
        $items = Auth::user()->items()->active()->orderBy('name')->get();
        
        // Generate next invoice number
        $invoiceNumber = Invoice::generateInvoiceNumber(Auth::id());
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('invoices.create', compact('clients', 'items', 'invoiceNumber', 'settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('invoices')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'reference_number' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:draft,pending,sent,viewed,paid,partial,overdue,cancelled',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'footer' => 'nullable|string',
            'notes' => 'nullable|string',
            'currency' => 'required|string|size:3',
            
            // Invoice items validation
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_type' => 'required|string|max:50',
            'items.*.is_taxable' => 'boolean',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_type' => 'nullable|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
        ]);
        
        $validated['user_id'] = Auth::id();
        
        // Verify client belongs to user
        $client = Auth::user()->clients()->findOrFail($validated['client_id']);
        
        DB::beginTransaction();
        
        try {
            // Create invoice
            $invoice = Invoice::create($validated);
            
            // Create invoice items
            foreach ($validated['items'] as $index => $itemData) {
                $itemData['invoice_id'] = $invoice->id;
                $itemData['sort_order'] = $index + 1;
                $itemData['is_taxable'] = isset($itemData['is_taxable']) && $itemData['is_taxable'];
                
                InvoiceItem::create($itemData);
            }
            
            // Calculate totals
            $invoice->calculateTotals();
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                            ->with('success', 'Invoice created successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $invoice->load(['client', 'invoiceItems.item', 'payments.creator']);
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('invoices.show', compact('invoice', 'settings'));
    }

    /**
     * Generate PDF for the invoice.
     */
    public function pdf(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $invoice->load(['client', 'invoiceItems.item']);
        $settings = \App\Models\Setting::forUser($invoice->user_id);
        
        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'settings'));
        
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Show preview of the invoice.
     */
    public function preview(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $invoice->load(['client', 'invoiceItems.item']);
        $settings = \App\Models\Setting::forUser($invoice->user_id);
        
        return view('invoices.preview', compact('invoice', 'settings'));
    }

    /**
     * Show preview of invoice from form data.
     */
    public function previewFromForm(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_type' => 'required|string|max:50',
        ]);

        // Create a temporary invoice object for preview
        $invoice = new Invoice();
        $invoice->id = 0; // Temporary ID
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->invoice_date = Carbon::parse($validated['invoice_date']);
        $invoice->due_date = Carbon::parse($validated['due_date']);
        $invoice->status = 'draft';
        $invoice->payment_status = 'unpaid';
        $invoice->subtotal = 0;
        $invoice->tax_amount = 0;
        $invoice->total_amount = 0;
        $invoice->balance_due = 0;
        $invoice->amount_paid = 0;
        
        // Load client
        $client = Client::find($validated['client_id']);
        $invoice->setRelation('client', $client);
        
        // Create temporary invoice items
        $invoiceItems = collect();
        $subtotal = 0;
        $taxAmount = 0;
        
        foreach ($validated['items'] as $itemData) {
            if (empty($itemData['item_name']) || empty($itemData['unit_price']) || empty($itemData['quantity'])) {
                continue;
            }
            
            $item = new InvoiceItem();
            $item->item_name = $itemData['item_name'];
            $item->item_description = $itemData['item_description'] ?? '';
            $item->unit_price = $itemData['unit_price'];
            $item->quantity = $itemData['quantity'];
            $item->unit_type = $itemData['unit_type'];
            $item->is_taxable = $itemData['is_taxable'] ?? false;
            $item->tax_rate = $itemData['tax_rate'] ?? 0;
            $item->tax_amount = 0;
            $item->discount_amount = 0;
            $item->line_total = $itemData['unit_price'] * $itemData['quantity'];
            $item->total_amount = $item->line_total;
            
            if ($item->is_taxable && $item->tax_rate > 0) {
                $item->tax_amount = $item->line_total * ($item->tax_rate / 100);
                $taxAmount += $item->tax_amount;
            }
            
            $subtotal += $item->line_total;
            $invoiceItems->push($item);
        }
        
        $invoice->subtotal = $subtotal;
        $invoice->tax_amount = $taxAmount;
        $invoice->total_amount = $subtotal + $taxAmount;
        $invoice->balance_due = $invoice->total_amount;
        
        $invoice->setRelation('invoiceItems', $invoiceItems);
        
        // Get user settings
        $settings = Setting::forUser(Auth::id());
        
        return view('invoices.preview', compact('invoice', 'settings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow editing paid invoices
        if ($invoice->payment_status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                           ->with('error', 'Cannot edit a paid invoice.');
        }
        
        $invoice->load(['invoiceItems']);
        $clients = Auth::user()->clients()->active()->orderBy('name')->get();
        $items = Auth::user()->items()->active()->orderBy('name')->get();
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('invoices.edit', compact('invoice', 'clients', 'items', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow editing paid invoices
        if ($invoice->payment_status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                           ->with('error', 'Cannot edit a paid invoice.');
        }
        
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('invoices')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($invoice->id)
            ],
            'reference_number' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:draft,pending,sent,viewed,paid,partial,overdue,cancelled',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'footer' => 'nullable|string',
            'notes' => 'nullable|string',
            'currency' => 'required|string|size:3',
            
            // Invoice items validation
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_type' => 'required|string|max:50',
            'items.*.is_taxable' => 'boolean',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_type' => 'nullable|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
        ]);
        
        // Verify client belongs to user
        $client = Auth::user()->clients()->findOrFail($validated['client_id']);
        
        DB::beginTransaction();
        
        try {
            // Update invoice
            $invoice->update($validated);
            
            // Delete existing items
            $invoice->invoiceItems()->delete();
            
            // Create new invoice items
            foreach ($validated['items'] as $index => $itemData) {
                $itemData['invoice_id'] = $invoice->id;
                $itemData['sort_order'] = $index + 1;
                $itemData['is_taxable'] = isset($itemData['is_taxable']) && $itemData['is_taxable'];
                
                InvoiceItem::create($itemData);
            }
            
            // Calculate totals
            $invoice->calculateTotals();
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                            ->with('success', 'Invoice updated successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update invoice: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow deleting paid invoices
        if ($invoice->payment_status === 'paid') {
            return redirect()->route('invoices.index')
                           ->with('error', 'Cannot delete a paid invoice.');
        }
        
        $invoice->delete();
        
        return redirect()->route('invoices.index')
                        ->with('success', 'Invoice deleted successfully!');
    }

    /**
     * Duplicate an existing invoice.
     */
    public function duplicate(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        DB::beginTransaction();
        
        try {
            $newInvoice = $invoice->replicate();
            $newInvoice->invoice_number = Invoice::generateInvoiceNumber(Auth::id());
            $newInvoice->status = 'draft';
            $newInvoice->payment_status = 'unpaid';
            $newInvoice->sent_date = null;
            $newInvoice->paid_date = null;
            $newInvoice->paid_amount = 0;
            $newInvoice->last_sent_at = null;
            $newInvoice->last_viewed_at = null;
            $newInvoice->view_count = 0;
            $newInvoice->invoice_date = today();
            $newInvoice->due_date = today()->addDays(30);
            $newInvoice->save();
            
            // Duplicate invoice items
            foreach ($invoice->invoiceItems as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }
            
            // Calculate totals
            $newInvoice->calculateTotals();
            
            DB::commit();
            
            return redirect()->route('invoices.edit', $newInvoice)
                            ->with('success', 'Invoice duplicated successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to duplicate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Mark invoice as sent.
     */
    public function markAsSent(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $invoice->markAsSent();
        
        return back()->with('success', 'Invoice marked as sent!');
    }

    /**
     * Add payment to invoice.
     */
    public function addPayment(Request $request, Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:' . $invoice->balance_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,bank_transfer,mobile_money,cheque',
            'payment_notes' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        try {
            // Add payment to invoice
            $invoice->addPayment($validated['payment_amount']);
            
            // Create payment record
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $validated['payment_amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['payment_notes'],
                'created_by' => Auth::id()
            ]);
            
            DB::commit();
            return back()->with('success', 'Payment added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to add payment: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice via email.
     */
    public function sendEmail(Request $request, Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string',
            'send_copy' => 'boolean',
        ]);
        
        try {
            $settings = Setting::forUser();
            $invoice->load(['client', 'invoiceItems.item']);
            
            // Log email attempt
            \Log::info('Attempting to send invoice email', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'recipient_email' => $validated['email'],
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
            ]);
            
            // Generate PDF
            $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'settings'));
            
            // Prepare email data
            $emailData = [
                'invoice' => $invoice,
                'settings' => $settings,
                'email_message' => $validated['message'] ?? '',
                'subject' => $validated['subject'],
            ];
            
            // Send email
            Mail::send('emails.invoice', $emailData, function ($mail) use ($validated, $invoice, $pdf, $settings, $emailData) {
                $mail->to($validated['email'])
                     ->subject($validated['subject'])
                     ->attachData($pdf->output(), 'invoice-' . $invoice->invoice_number . '.pdf', [
                         'mime' => 'application/pdf',
                     ]);
                
                // Send copy to user if requested
                if (isset($validated['send_copy']) && $validated['send_copy']) {
                    $copyEmail = $settings->company_email ?? auth()->user()->email;
                    $mail->cc($copyEmail);
                    \Log::info('Sending copy to: ' . $copyEmail);
                }
                
                // Set from address
                $fromEmail = $settings->company_email ?? config('mail.from.address');
                $fromName = $settings->company_name ?? config('mail.from.name');
                $mail->from($fromEmail, $fromName);
                
                \Log::info('Email configured', [
                    'from_email' => $fromEmail,
                    'from_name' => $fromName,
                    'to_email' => $validated['email'],
                    'subject' => $validated['subject']
                ]);
            });
            
            // Update invoice status
            if ($invoice->status === 'draft') {
                $invoice->markAsSent();
                \Log::info('Invoice status updated to sent for invoice: ' . $invoice->invoice_number);
            }
            
            // Update last sent timestamp
            $invoice->update([
                'last_sent_at' => now(),
            ]);
            
            \Log::info('Invoice email sent successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'recipient_email' => $validated['email']
            ]);
            
            return back()->with('success', 'Invoice sent successfully to ' . $validated['email'] . '! Check your email logs for confirmation.');
            
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            \Log::error('SMTP Transport Error when sending invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'SMTP Error: Unable to connect to mail server. Please check your email configuration. Error: ' . $e->getMessage());
            
        } catch (\Swift_TransportException $e) {
            \Log::error('Swift Transport Error when sending invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Mail Transport Error: ' . $e->getMessage());
            
        } catch (\Exception $e) {
            \Log::error('General error when sending invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }
    
    /**
     * Get invoice statistics.
     */
    private function getInvoiceStats()
    {
        $userId = Auth::id();
        
        return [
            'total_count' => Invoice::where('user_id', $userId)->count(),
            'draft_count' => Invoice::where('user_id', $userId)->where('status', 'draft')->count(),
            'pending_count' => Invoice::where('user_id', $userId)->where('status', 'pending')->count(),
            'sent_count' => Invoice::where('user_id', $userId)->where('status', 'sent')->count(),
            'paid_count' => Invoice::where('user_id', $userId)->where('payment_status', 'paid')->count(),
            'overdue_count' => Invoice::where('user_id', $userId)->overdue()->count(),
            'total_amount' => Invoice::where('user_id', $userId)->sum('total_amount'),
            'paid_amount' => Invoice::where('user_id', $userId)->sum('paid_amount'),
            'outstanding_amount' => Invoice::where('user_id', $userId)->sum('balance_due'),
        ];
    }
}
