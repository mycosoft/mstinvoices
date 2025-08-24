<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
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
        $query = Auth::user()->items();
        
        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        // Handle status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Handle category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        // Handle item type filter
        if ($request->has('type') && $request->type !== '') {
            $isService = $request->type === 'service';
            $query->where('is_service', $isService);
        }
        
        $items = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get categories for filter dropdown
        $categories = Item::getCategoriesForUser(Auth::id());
        
        // Get user settings for currency
        $settings = Setting::forUser();
        
        return view('items.index', compact('items', 'categories', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Item::getCategoriesForUser(Auth::id());
        $settings = Setting::forUser();
        return view('items.create', compact('categories', 'settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'unit_price' => 'required|numeric|min:0|max:999999.99',
            'cost_price' => 'nullable|numeric|min:0|max:999999.99',
            'unit_type' => 'required|string|max:50',
            'is_taxable' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'is_service' => 'boolean',
            'notes' => 'nullable|string',
            'image_url' => 'nullable|url|max:255',
        ]);
        
        $validated['user_id'] = Auth::id();
        
        // Set defaults
        $validated['is_taxable'] = $request->has('is_taxable');
        $validated['is_service'] = $request->has('is_service');
        
        $item = Item::create($validated);
        
        return redirect()->route('items.show', $item)
                        ->with('success', 'Item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Ensure the item belongs to the authenticated user
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $settings = Setting::forUser();
        
        return view('items.show', compact('item', 'settings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Ensure the item belongs to the authenticated user
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = Item::getCategoriesForUser(Auth::id());
        $settings = Setting::forUser();
        return view('items.edit', compact('item', 'categories', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Ensure the item belongs to the authenticated user
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'unit_price' => 'required|numeric|min:0|max:999999.99',
            'cost_price' => 'nullable|numeric|min:0|max:999999.99',
            'unit_type' => 'required|string|max:50',
            'is_taxable' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'is_service' => 'boolean',
            'notes' => 'nullable|string',
            'image_url' => 'nullable|url|max:255',
        ]);
        
        // Set defaults
        $validated['is_taxable'] = $request->has('is_taxable');
        $validated['is_service'] = $request->has('is_service');
        
        $item->update($validated);
        
        return redirect()->route('items.show', $item)
                        ->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Ensure the item belongs to the authenticated user
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $item->delete();
        
        return redirect()->route('items.index')
                        ->with('success', 'Item deleted successfully!');
    }
}
