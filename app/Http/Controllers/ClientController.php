<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientController extends Controller
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
        $query = Auth::user()->clients();
        
        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Handle status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $clients = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'business_type' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        
        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }
        
        $validated['user_id'] = Auth::id();
        
        $client = Client::create($validated);
        
        // Check if request is AJAX (for modal form submission)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Client created successfully!',
                'client' => $client
            ]);
        }
        
        return redirect()->route('clients.show', $client)
                        ->with('success', 'Client created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // Ensure the client belongs to the authenticated user
        if ($client->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Calculate client statistics
        $invoices = $client->invoices;
        $stats = [
            'total_invoices' => $invoices->count(),
            'total_revenue' => $invoices->where('payment_status', 'paid')->sum('total_amount'),
            'pending_amount' => $invoices->whereIn('payment_status', ['unpaid', 'partial'])->sum('balance_due'),
            'paid_amount' => $invoices->sum('amount_paid'),
            'overdue_amount' => $invoices->where('status', 'overdue')->sum('balance_due'),
        ];
        
        // Get user settings for currency formatting
        $settings = Setting::forUser();
        
        return view('clients.show', compact('client', 'stats', 'settings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        // Ensure the client belongs to the authenticated user
        if ($client->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        // Ensure the client belongs to the authenticated user
        if ($client->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($client->id)
            ],
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'business_type' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        
        $client->update($validated);
        
        return redirect()->route('clients.show', $client)
                        ->with('success', 'Client updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Ensure the client belongs to the authenticated user
        if ($client->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $client->delete();
        
        return redirect()->route('clients.index')
                        ->with('success', 'Client deleted successfully!');
    }
}
