<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Client;
use App\Services\NameSiloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DomainController extends Controller
{
    protected NameSiloService $nameSiloService;

    public function __construct(NameSiloService $nameSiloService)
    {
        $this->middleware('auth');
        $this->nameSiloService = $nameSiloService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Update expired domain statuses before displaying
        Domain::updateExpiredStatuses();
        
        $query = Auth::user()->domains()->with('client');
        
        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('domain_name', 'like', "%{$search}%")
                  ->orWhere('registrar', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }
        
        // Handle status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Handle expiry filter
        if ($request->has('expiry_filter') && $request->expiry_filter) {
            switch ($request->expiry_filter) {
                case 'expiring_soon':
                    $query->expiringSoon(30);
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'expires_this_month':
                    $query->whereBetween('expiry_date', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
                    break;
            }
        }
        
        $domains = $query->orderBy('expiry_date', 'asc')->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => Auth::user()->domains()->count(),
            'active' => Auth::user()->domains()->active()->count(),
            'expired' => Auth::user()->domains()->expired()->count(),
            'expiring_soon' => Auth::user()->domains()->expiringSoon(30)->count(),
            'total_cost' => Auth::user()->domains()->sum('registration_cost') + Auth::user()->domains()->sum('renewal_cost'),
        ];
        
        return view('domains.index', compact('domains', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Auth::user()->clients()->active()->get();
        $supportedTlds = $this->nameSiloService->getSupportedTlds();
        
        return view('domains.create', compact('clients', 'supportedTlds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?\.[a-zA-Z]{2,}$/',
                Rule::unique('domains')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'client_id' => 'nullable|exists:clients,id',
            'registration_date' => 'required|date',
            'expiry_date' => 'required|date|after:registration_date',
            'registration_cost' => 'nullable|numeric|min:0',
            'renewal_cost' => 'nullable|numeric|min:0',
            'registrar' => 'required|string|max:100',
            'auto_renew' => 'boolean',
            'privacy_protection' => 'boolean',
            'contact_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        // Validate domain name format
        if (!$this->nameSiloService->validateDomainName($validated['domain_name'])) {
            return back()->withErrors(['domain_name' => 'Invalid domain name format.'])->withInput();
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';
        
        $domain = Domain::create($validated);
        
        return redirect()->route('domains.show', $domain)
                        ->with('success', 'Domain added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain)
    {
        // Ensure the domain belongs to the authenticated user
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Try to get fresh data from NameSilo API
        $apiData = null;
        try {
            $apiData = $this->nameSiloService->getDomainInfo($domain->domain_name);
            // Ensure nameservers is always an array
            if (isset($apiData['nameservers']) && !is_array($apiData['nameservers'])) {
                $apiData['nameservers'] = [];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch domain info from NameSilo: ' . $e->getMessage());
        }
        
        return view('domains.show', compact('domain', 'apiData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Domain $domain)
    {
        // Ensure the domain belongs to the authenticated user
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $clients = Auth::user()->clients()->active()->get();
        
        return view('domains.edit', compact('domain', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Domain $domain)
    {
        // Ensure the domain belongs to the authenticated user
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'required|in:active,expired,suspended,pending',
            'registration_date' => 'required|date',
            'expiry_date' => 'required|date|after:registration_date',
            'renewal_date' => 'nullable|date',
            'registration_cost' => 'nullable|numeric|min:0',
            'renewal_cost' => 'nullable|numeric|min:0',
            'registrar' => 'required|string|max:100',
            'auto_renew' => 'boolean',
            'privacy_protection' => 'boolean',
            'contact_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);
        
        $domain->update($validated);
        
        return redirect()->route('domains.show', $domain)
                        ->with('success', 'Domain updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domain $domain)
    {
        // Ensure the domain belongs to the authenticated user
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $domain->delete();
        
        return redirect()->route('domains.index')
                        ->with('success', 'Domain deleted successfully!');
    }

    /**
     * Check domain availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255'
        ]);

        try {
            $result = $this->nameSiloService->checkAvailability($request->domain);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register domain via NameSilo API
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255',
            'years' => 'required|integer|min:1|max:10',
            'privacy_protection' => 'boolean',
            'auto_renew' => 'boolean',
            'contact_email' => 'nullable|email|max:255',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        try {
            $result = $this->nameSiloService->registerDomain($validated);
            
            if ($result['success']) {
                // Create domain record in database
                $domain = Domain::create([
                    'user_id' => Auth::id(),
                    'client_id' => $validated['client_id'] ?? null,
                    'domain_name' => $validated['domain_name'],
                    'status' => 'active',
                    'registration_date' => Carbon::now(),
                    'expiry_date' => Carbon::now()->addYears($validated['years']),
                    'registrar' => 'NameSilo',
                    'auto_renew' => $validated['auto_renew'] ?? false,
                    'privacy_protection' => $validated['privacy_protection'] ?? false,
                    'contact_email' => $validated['contact_email'] ?? null,
                    'api_data' => $result,
                ]);

                return redirect()->route('domains.show', $domain)
                                ->with('success', 'Domain registered successfully!');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Renew domain via NameSilo API
     */
    public function renew(Request $request, Domain $domain)
    {
        // Ensure the domain belongs to the authenticated user
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'years' => 'required|integer|min:1|max:10',
        ]);

        try {
            $result = $this->nameSiloService->renewDomain($domain->domain_name, $validated['years']);
            
            if ($result['success']) {
                // Update domain record
                $domain->update([
                    'expiry_date' => Carbon::parse($result['new_expiry']),
                    'renewal_date' => Carbon::now(),
                    'api_data' => array_merge($domain->api_data ?? [], $result),
                ]);

                return redirect()->route('domains.show', $domain)
                                ->with('success', 'Domain renewed successfully!');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update nameservers
     */
    public function updateNameservers(Request $request, Domain $domain)
    {
        // Ensure the domain belongs to the authenticated user
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nameservers' => 'required|array|min:1|max:4',
            'nameservers.*' => 'required|string|max:255',
        ]);

        try {
            $result = $this->nameSiloService->updateNameservers($domain->domain_name, $validated['nameservers']);
            
            if ($result['success']) {
                $domain->update([
                    'nameservers' => $validated['nameservers'],
                    'api_data' => array_merge($domain->api_data ?? [], $result),
                ]);

                return redirect()->route('domains.show', $domain)
                                ->with('success', 'Nameservers updated successfully!');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Sync domain data from NameSilo API
     */
    public function sync(Domain $domain)
    {
        // Ensure the domain belongs to the authenticated user
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $apiData = $this->nameSiloService->getDomainInfo($domain->domain_name);
            
            if ($apiData['success']) {
                $updateData = [
                    'status' => $apiData['status'],
                    'expiry_date' => Carbon::parse($apiData['expires']),
                    'auto_renew' => $apiData['auto_renew'] === 'yes',
                    'privacy_protection' => $apiData['private'] === 'yes',
                    'api_data' => $apiData,
                ];

                // Only update nameservers if they exist
                if (!empty($apiData['nameservers'])) {
                    $updateData['nameservers'] = $apiData['nameservers'];
                }

                $domain->update($updateData);

                return redirect()->route('domains.show', $domain)
                                ->with('success', 'Domain data synced successfully!');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get domain pricing
     */
    public function getPricing(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255'
        ]);

        try {
            $result = $this->nameSiloService->getDomainPricing($request->domain);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync domains from NameSilo account
     */
    public function syncFromAccount()
    {
        try {
            $result = $this->nameSiloService->syncDomainsFromAccount(Auth::id());
            
            if ($result['success']) {
                $message = "Successfully synced {$result['total_processed']} domains from NameSilo account.";
                if (!empty($result['errors'])) {
                    $message .= " Errors: " . implode(', ', $result['errors']);
                }
                
                return redirect()->route('domains.index')
                                ->with('success', $message);
            }
        } catch (\Exception $e) {
            return redirect()->route('domains.index')
                            ->withErrors(['error' => 'Failed to sync domains: ' . $e->getMessage()]);
        }
    }

    /**
     * List domains from NameSilo account (API endpoint)
     */
    public function listFromAccount()
    {
        try {
            $result = $this->nameSiloService->listDomains();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
