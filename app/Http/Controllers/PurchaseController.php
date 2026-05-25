<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Purchase::where('user_id', Auth::id())->with('supplier');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('purchase_number', 'like', "%{$request->search}%")
                  ->orWhere('reference', 'like', "%{$request->search}%");
            });
        }
        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(20);
        $suppliers = Supplier::where('user_id', Auth::id())->active()->orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('user_id', Auth::id())->active()->orderBy('name')->get();
        $items = Item::where('user_id', Auth::id())->active()->orderBy('name')->get();
        $warehouses = Warehouse::where('user_id', Auth::id())->active()->get();

        return view('purchases.create', compact('suppliers', 'items', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reference' => 'nullable|string|max:100',
            'purchase_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:purchase_date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'required_without:items.*.item_id|string|max:255',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $purchase = Purchase::create([
            'user_id' => Auth::id(),
            'supplier_id' => $validated['supplier_id'],
            'reference' => $validated['reference'],
            'purchase_date' => $validated['purchase_date'],
            'delivery_date' => $validated['delivery_date'],
            'warehouse_id' => $validated['warehouse_id'],
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
            'status' => 'ordered',
            'payment_status' => 'unpaid',
        ]);

        foreach ($validated['items'] as $itemData) {
            $item_name = $itemData['item_name'];
            $unit_cost = $itemData['unit_cost'];
            $quantity = $itemData['quantity'];
            $tax_rate = $itemData['tax_rate'] ?? 0;
            $line_total = $unit_cost * $quantity;
            $tax_amount = $line_total * ($tax_rate / 100);
            $total_amount = $line_total + $tax_amount;

            $item = null;
            if (!empty($itemData['item_id'])) {
                $item = Item::find($itemData['item_id']);
                $item_name = $item->name;
            }

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $itemData['item_id'] ?? null,
                'item_name' => $item_name,
                'unit_cost' => $unit_cost,
                'quantity' => $quantity,
                'unit_type' => $item->unit_type ?? 'piece',
                'line_total' => $line_total,
                'tax_rate' => $tax_rate,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
            ]);
        }

        $purchase->calculateTotals();

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase order created successfully!');
    }

    public function show(Purchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) abort(403);
        $purchase->load(['supplier', 'items', 'payments.creator', 'warehouse']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) abort(403);
        if (in_array($purchase->status, ['received', 'paid', 'cancelled'])) {
            return back()->with('error', 'Cannot edit a purchase that has been received, paid or cancelled.');
        }

        $suppliers = Supplier::where('user_id', Auth::id())->active()->get();
        $items = Item::where('user_id', Auth::id())->active()->get();
        $warehouses = Warehouse::where('user_id', Auth::id())->active()->get();
        $purchase->load('items');

        return view('purchases.edit', compact('purchase', 'suppliers', 'items', 'warehouses'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) abort(403);
        if (in_array($purchase->status, ['received', 'paid', 'cancelled'])) {
            return back()->with('error', 'Cannot update a received/paid/cancelled purchase.');
        }

        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reference' => 'nullable|string|max:100',
            'purchase_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'required_without:items.*.item_id|string',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $purchase->update([
            'supplier_id' => $validated['supplier_id'],
            'reference' => $validated['reference'],
            'purchase_date' => $validated['purchase_date'],
            'delivery_date' => $validated['delivery_date'],
            'warehouse_id' => $validated['warehouse_id'],
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
        ]);

        $purchase->items()->delete();

        foreach ($validated['items'] as $itemData) {
            $line_total = $itemData['unit_cost'] * $itemData['quantity'];
            $tax_amount = $line_total * (($itemData['tax_rate'] ?? 0) / 100);
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $itemData['item_id'] ?? null,
                'item_name' => $itemData['item_name'],
                'unit_cost' => $itemData['unit_cost'],
                'quantity' => $itemData['quantity'],
                'line_total' => $line_total,
                'tax_rate' => $itemData['tax_rate'] ?? 0,
                'tax_amount' => $tax_amount,
                'total_amount' => $line_total + $tax_amount,
            ]);
        }

        $purchase->calculateTotals();

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase updated successfully!');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) abort(403);
        if (in_array($purchase->status, ['received', 'partial', 'paid'])) {
            return back()->with('error', 'Cannot delete a received or paid purchase.');
        }

        $purchase->items()->delete();
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase deleted successfully!');
    }

    public function receive(Request $request, Purchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) abort(403);
        if ($purchase->status === 'cancelled') {
            return back()->with('error', 'Cannot receive a cancelled purchase.');
        }

        $purchase->update([
            'status' => 'received',
            'delivery_date' => $request->delivery_date ?? now()->toDateString(),
        ]);

        // Create stock movements and update inventory
        foreach ($purchase->items as $purchaseItem) {
            if ($purchaseItem->item_id) {
                StockMovement::recordMovement(
                    $purchaseItem->item,
                    'in',
                    $purchaseItem->quantity,
                    $purchaseItem->unit_cost,
                    'purchase',
                    $purchase->id,
                    'Purchase: ' . $purchase->purchase_number
                );
            }
        }

        // Create journal entry
        $accountingService = app(AccountingService::class);
        $accountingService->postPurchaseReceipt(
            $purchase->total_amount,
            $purchase->tax_amount,
            $purchase->items->whereNotNull('item_id')->sum('line_total'),
            $purchase->items->whereNull('item_id')->sum('line_total'),
            $purchase->id
        );

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase received successfully! Stock updated and journal entry created.');
    }

    public function addPayment(Request $request, Purchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $purchase->balance_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payment = PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'],
            'notes' => $validated['notes'],
            'created_by' => Auth::id(),
        ]);

        $purchase->paid_amount += $validated['amount'];
        $purchase->balance_due = max(0, $purchase->total_amount - $purchase->paid_amount);

        if ($purchase->balance_due <= 0) {
            $purchase->payment_status = 'paid';
            $purchase->status = 'paid';
        } else {
            $purchase->payment_status = 'partial';
        }
        $purchase->save();

        // Create journal entry for purchase payment
        $accountingService = app(AccountingService::class);
        $accountingService->postPurchasePayment($validated['amount'], $purchase->id);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Payment added successfully! Journal entry created.');
    }
}
