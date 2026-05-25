<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $items = Item::where('user_id', Auth::id())
            ->where('track_inventory', true)
            ->with('stockMovements')
            ->orderBy('name')
            ->paginate(20);

        $lowStockCount = Item::where('user_id', Auth::id())
            ->where('track_inventory', true)
            ->lowStock()
            ->count();

        $totalValue = Item::where('user_id', Auth::id())
            ->where('track_inventory', true)
            ->selectRaw('SUM(stock_quantity * cost_price) as total_value')
            ->value('total_value') ?? 0;

        $totalItems = Item::where('user_id', Auth::id())
            ->where('track_inventory', true)
            ->sum('stock_quantity');

        return view('inventory.index', compact('items', 'lowStockCount', 'totalValue', 'totalItems'));
    }

    public function adjustments()
    {
        $movements = StockMovement::where('user_id', Auth::id())
            ->where('type', 'adjustment')
            ->with('item')
            ->orderBy('movement_date', 'desc')
            ->paginate(20);

        $items = Item::where('user_id', Auth::id())
            ->where('track_inventory', true)
            ->active()
            ->orderBy('name')
            ->get();

        return view('inventory.adjustments', compact('movements', 'items'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'new_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::where('user_id', Auth::id())->findOrFail($validated['item_id']);
        $oldQuantity = $item->stock_quantity ?? 0;

        StockMovement::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'type' => 'adjustment',
            'quantity' => $validated['new_quantity'],
            'unit_cost' => $item->cost_price,
            'reference_type' => 'manual_adjustment',
            'notes' => "Adjusted from {$oldQuantity} to {$validated['new_quantity']}. " . $validated['notes'],
            'movement_date' => now()->toDateString(),
        ]);

        $item->update(['stock_quantity' => $validated['new_quantity']]);

        return redirect()->route('inventory.adjustments')
            ->with('success', 'Stock adjusted successfully!');
    }

    public function movements(Request $request)
    {
        $query = StockMovement::where('user_id', Auth::id())->with(['item', 'warehouse']);

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('item_id') && $request->item_id) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('movement_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('movement_date', '<=', $request->date_to);
        }

        $movements = $query->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25);

        $items = Item::where('user_id', Auth::id())
            ->where('track_inventory', true)
            ->active()
            ->orderBy('name')
            ->get();

        return view('inventory.movements', compact('movements', 'items'));
    }

    public function lowStock()
    {
        $items = Item::where('user_id', Auth::id())
            ->where('track_inventory', true)
            ->lowStock()
            ->active()
            ->orderBy('name')
            ->get();

        return view('inventory.low-stock', compact('items'));
    }
}
