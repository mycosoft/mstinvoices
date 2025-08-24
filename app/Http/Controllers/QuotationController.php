<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
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

class QuotationController extends Controller
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
        $query = Auth::user()->quotations()->with(['client', 'quotationItems']);
        
        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
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
        
        // Handle client filter
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        
        // Handle date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('quotation_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('quotation_date', '<=', $request->date_to);
        }
        
        // Handle expired filter
        if ($request->has('expired') && $request->expired) {
            $query->expired();
        }
        
        $quotations = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get clients for filter dropdown
        $clients = Auth::user()->clients()->active()->orderBy('name')->get();
        
        // Calculate summary statistics
        $stats = $this->getQuotationStats();
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('quotations.index', compact('quotations', 'clients', 'stats', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Auth::user()->clients()->active()->orderBy('name')->get();
        $items = Auth::user()->items()->active()->orderBy('name')->get();
        
        // Generate next quotation number
        $quotationNumber = Quotation::generateQuotationNumber(Auth::id());
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('quotations.create', compact('clients', 'items', 'quotationNumber', 'settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quotation_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('quotations')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'reference_number' => 'nullable|string|max:255',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quotation_date',
            'status' => 'required|in:draft,sent,viewed,accepted,rejected,expired,converted',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'footer' => 'nullable|string',
            'notes' => 'nullable|string',
            'currency' => 'required|string|size:3',
            
            // Quotation items validation
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
            // Create quotation
            $quotation = Quotation::create($validated);
            
            // Create quotation items
            foreach ($validated['items'] as $index => $itemData) {
                $itemData['quotation_id'] = $quotation->id;
                $itemData['sort_order'] = $index + 1;
                $itemData['is_taxable'] = isset($itemData['is_taxable']) && $itemData['is_taxable'];
                
                QuotationItem::create($itemData);
            }
            
            // Calculate totals
            $quotation->calculateTotals();
            
            DB::commit();
            
            return redirect()->route('quotations.show', $quotation)
                            ->with('success', 'Quotation created successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create quotation: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $quotation->load(['client', 'quotationItems.item']);
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('quotations.show', compact('quotation', 'settings'));
    }

    /**
     * Generate PDF for the quotation.
     */
    public function pdf(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $quotation->load(['client', 'quotationItems.item']);
        $settings = \App\Models\Setting::forUser($quotation->user_id);
        
        $pdf = PDF::loadView('quotations.pdf', compact('quotation', 'settings'));
        
        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    }

    /**
     * Show preview of the quotation.
     */
    public function preview(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $quotation->load(['client', 'quotationItems.item']);
        $settings = \App\Models\Setting::forUser($quotation->user_id);
        
        return view('quotations.preview', compact('quotation', 'settings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow editing accepted/converted quotations
        if (in_array($quotation->status, ['accepted', 'converted'])) {
            return redirect()->route('quotations.show', $quotation)
                           ->with('error', 'Cannot edit an accepted or converted quotation.');
        }
        
        $quotation->load(['quotationItems']);
        $clients = Auth::user()->clients()->active()->orderBy('name')->get();
        $items = Auth::user()->items()->active()->orderBy('name')->get();
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('quotations.edit', compact('quotation', 'clients', 'items', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow editing accepted/converted quotations
        if (in_array($quotation->status, ['accepted', 'converted'])) {
            return redirect()->route('quotations.show', $quotation)
                           ->with('error', 'Cannot edit an accepted or converted quotation.');
        }
        
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quotation_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('quotations')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($quotation->id)
            ],
            'reference_number' => 'nullable|string|max:255',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quotation_date',
            'status' => 'required|in:draft,sent,viewed,accepted,rejected,expired,converted',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'footer' => 'nullable|string',
            'notes' => 'nullable|string',
            'currency' => 'required|string|size:3',
            
            // Quotation items validation
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
            // Update quotation
            $quotation->update($validated);
            
            // Delete existing items
            $quotation->quotationItems()->delete();
            
            // Create new quotation items
            foreach ($validated['items'] as $index => $itemData) {
                $itemData['quotation_id'] = $quotation->id;
                $itemData['sort_order'] = $index + 1;
                $itemData['is_taxable'] = isset($itemData['is_taxable']) && $itemData['is_taxable'];
                
                QuotationItem::create($itemData);
            }
            
            // Calculate totals
            $quotation->calculateTotals();
            
            DB::commit();
            
            return redirect()->route('quotations.show', $quotation)
                            ->with('success', 'Quotation updated successfully!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update quotation: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow deletion of accepted/converted quotations
        if (in_array($quotation->status, ['accepted', 'converted'])) {
            return redirect()->route('quotations.index')
                           ->with('error', 'Cannot delete an accepted or converted quotation.');
        }
        
        try {
            $quotation->delete();
            return redirect()->route('quotations.index')
                           ->with('success', 'Quotation deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('quotations.index')
                           ->with('error', 'Failed to delete quotation: ' . $e->getMessage());
        }
    }

    /**
     * Mark quotation as accepted.
     */
    public function accept(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $quotation->markAsAccepted();
        
        return redirect()->route('quotations.show', $quotation)
                        ->with('success', 'Quotation marked as accepted!');
    }

    /**
     * Mark quotation as rejected.
     */
    public function reject(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $quotation->markAsRejected();
        
        return redirect()->route('quotations.show', $quotation)
                        ->with('success', 'Quotation marked as rejected!');
    }

    /**
     * Convert quotation to invoice.
     */
    public function convertToInvoice(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($quotation->status !== 'accepted') {
            return redirect()->route('quotations.show', $quotation)
                           ->with('error', 'Only accepted quotations can be converted to invoices.');
        }
        
        try {
            $invoice = $quotation->convertToInvoice();
            
            return redirect()->route('invoices.show', $invoice)
                           ->with('success', 'Quotation converted to invoice successfully!');
        } catch (\Exception $e) {
            return redirect()->route('quotations.show', $quotation)
                           ->with('error', 'Failed to convert quotation: ' . $e->getMessage());
        }
    }

    /**
     * Send quotation via email.
     */
    public function sendEmail(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated user
        if ($quotation->user_id !== Auth::id()) {
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
            $quotation->load(['client', 'quotationItems.item']);
            
            // Log email attempt
            \Log::info('Attempting to send quotation email', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'recipient_email' => $validated['email'],
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
            ]);
            
            // Generate PDF
            $pdf = PDF::loadView('quotations.pdf', compact('quotation', 'settings'));
            
            // Prepare email data
            $emailData = [
                'quotation' => $quotation,
                'settings' => $settings,
                'email_message' => $validated['message'] ?? '',
                'subject' => $validated['subject'],
            ];
            
            // Send email
            Mail::send('emails.quotation', $emailData, function ($mail) use ($validated, $quotation, $pdf, $settings, $emailData) {
                $mail->to($validated['email'])
                     ->subject($validated['subject'])
                     ->attachData($pdf->output(), 'quotation-' . $quotation->quotation_number . '.pdf', [
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
            
            // Update quotation status
            if ($quotation->status === 'draft') {
                $quotation->markAsSent();
                \Log::info('Quotation status updated to sent for quotation: ' . $quotation->quotation_number);
            }
            
            // Update last sent timestamp
            $quotation->update([
                'last_sent_at' => now(),
            ]);
            
            \Log::info('Quotation email sent successfully', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'recipient_email' => $validated['email']
            ]);
            
            return back()->with('success', 'Quotation sent successfully to ' . $validated['email'] . '! Check your email logs for confirmation.');
            
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            \Log::error('SMTP Transport Error when sending quotation email', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'SMTP Error: Unable to connect to mail server. Please check your email configuration. Error: ' . $e->getMessage());
            
        } catch (\Swift_TransportException $e) {
            \Log::error('Swift Transport Error when sending quotation email', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Mail Transport Error: ' . $e->getMessage());
            
        } catch (\Exception $e) {
            \Log::error('General error when sending quotation email', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to send quotation: ' . $e->getMessage());
        }
    }

    /**
     * Get quotation statistics.
     */
    private function getQuotationStats()
    {
        $quotationsQuery = Auth::user()->quotations();
        
        // Get all quotations for calculations
        $allQuotations = $quotationsQuery->get();
        
        // Get basic counts
        $total = $allQuotations->count();
        $draft = $allQuotations->where('status', 'draft')->count();
        $sent = $allQuotations->where('status', 'sent')->count();
        $accepted = $allQuotations->where('status', 'accepted')->count();
        $rejected = $allQuotations->where('status', 'rejected')->count();
        $converted = $allQuotations->where('status', 'converted')->count();
        
        // Get expired count (quotations past valid_until date and not accepted/rejected/converted)
        $expired = $allQuotations->filter(function ($quotation) {
            return $quotation->is_expired;
        })->count();
        
        // Calculate totals
        $totalValue = $allQuotations->sum('total_amount');
        $acceptedValue = $allQuotations->where('status', 'accepted')->sum('total_amount');
        
        return [
            'total' => $total,
            'draft' => $draft,
            'sent' => $sent,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'expired' => $expired,
            'converted' => $converted,
            'total_value' => $totalValue,
            'accepted_value' => $acceptedValue,
            'acceptance_rate' => $sent > 0 ? round(($accepted / $sent) * 100, 2) : 0,
        ];
    }
}
