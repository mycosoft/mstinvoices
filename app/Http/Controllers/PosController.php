<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\StockMovement;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the POS terminal.
     */
    public function terminal()
    {
        $items = Item::where('user_id', Auth::id())
            ->active()
            ->where('is_service', false)
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $categories = Item::where('user_id', Auth::id())
            ->where('is_service', false)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $clients = Client::where('user_id', Auth::id())
            ->active()
            ->orderBy('name')
            ->get();

        $settings = \App\Models\Setting::forUser(Auth::id());

        return view('pos.terminal', compact('items', 'categories', 'clients', 'settings'));
    }

    /**
     * Process a POS sale.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'payment_method' => 'required|in:cash,credit_card,debit_card,mobile_money,bank_transfer,credit,other',
            'amount_paid' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        // Prevent credit for walk-in customers
        if ($validated['payment_method'] === 'credit' && empty($validated['client_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Credit (on account) is only available for registered clients. Please select a client or choose another payment method.',
            ], 422);
        }

        $userId = Auth::id();

        return DB::transaction(function () use ($validated, $userId) {
            $subtotal = 0;
            $taxAmount = 0;
            $totalDiscount = $validated['discount_amount'] ?? 0;
            $cogsAmount = 0;

            // Calculate totals from items (tax is optional at POS)
            foreach ($validated['items'] as $i => $itemData) {
                $item = Item::find($itemData['item_id']);
                $lineTotal = $itemData['unit_price'] * $itemData['quantity'];

                $subtotal += $lineTotal;

                // Calculate COGS for tracked inventory items
                if ($item->track_inventory && $item->cost_price) {
                    $cogsAmount += $item->cost_price * $itemData['quantity'];
                }
            }

            $totalAmount = $subtotal - $totalDiscount;
            $paidAmount = min($validated['amount_paid'], $totalAmount);
            $changeAmount = max(0, $validated['amount_paid'] - $totalAmount);

            // Create POS sale
            $sale = PosSale::create([
                'user_id' => $userId,
                'client_id' => $validated['client_id'],
                'sale_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $totalDiscount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'notes' => $validated['notes'],
                'cashier_name' => Auth::user()->name,
            ]);

            // Create sale items & update stock
            foreach ($validated['items'] as $itemData) {
                $item = Item::find($itemData['item_id']);
                $lineTotal = $itemData['unit_price'] * $itemData['quantity'];
                $itemDiscount = $itemData['discount'] ?? 0;

                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'unit_type' => $item->unit_type,
                    'line_total' => $lineTotal,
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => $itemDiscount,
                    'total_amount' => $lineTotal - $itemDiscount,
                ]);

                // Reduce stock
                if ($item->track_inventory) {
                    StockMovement::recordMovement(
                        $item, 'out', $itemData['quantity'],
                        $item->cost_price, 'pos_sale', $sale->id,
                        'POS Sale: ' . $sale->pos_number
                    );
                }
            }

            // Create journal entry
            $accountingService = app(AccountingService::class);
            $accountingService->postPosSale(
                $paidAmount,
                $taxAmount,
                $totalDiscount,
                $cogsAmount,
                $validated['payment_method'],
                $validated['client_id'],
                $sale->id
            );

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'pos_number' => $sale->pos_number,
                'change_amount' => $changeAmount,
                'message' => 'Sale completed successfully!',
            ]);
        });
    }

    /**
     * Search items (AJAX).
     */
    public function searchItems(Request $request)
    {
        $query = Item::where('user_id', Auth::id())
            ->active()
            ->where('is_service', false);

        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $items = $query->orderBy('name')->get()->map(function ($item) {
            $outOfStock = $item->track_inventory && $item->stock_quantity <= 0;
            return [
                'id' => $item->id,
                'name' => $item->name,
                'unit_price' => $item->unit_price,
                'unit_type' => $item->unit_type,
                'category' => $item->category,
                'is_taxable' => $item->is_taxable,
                'tax_rate' => $item->tax_rate,
                'track_inventory' => $item->track_inventory,
                'stock_quantity' => $item->stock_quantity,
                'out_of_stock' => $outOfStock,
                'image_url' => $item->image_url,
            ];
        });

        return response()->json($items);
    }

    /**
     * List POS sales.
     */
    public function index(Request $request)
    {
        $query = PosSale::where('user_id', Auth::id())->with('client', 'saleItems');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('sale_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('sale_date', '<=', $request->date_to);
        }

        $sales = $query->orderBy('sale_date', 'desc')->orderBy('id', 'desc')->paginate(20);

        return view('pos.index', compact('sales'));
    }

    /**
     * Show POS sale detail.
     */
    public function show(PosSale $posSale)
    {
        if ($posSale->user_id !== Auth::id()) abort(403);
        $posSale->load(['client', 'saleItems.item', 'invoice']);
        return view('pos.show', compact('posSale'));
    }

    /**
     * Show daily POS summary.
     */
    public function dailySummary()
    {
        $todaySales = PosSale::where('user_id', Auth::id())
            ->whereDate('sale_date', today());

        $summary = [
            'total_sales' => $todaySales->count(),
            'total_revenue' => $todaySales->sum('total_amount'),
            'total_tax' => $todaySales->sum('tax_amount'),
            'total_paid' => $todaySales->sum('paid_amount'),
            'payment_breakdown' => $todaySales->selectRaw('payment_method, SUM(total_amount) as total')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method'),
        ];

        // This week
        $weekSales = PosSale::where('user_id', Auth::id())
            ->where('sale_date', '>=', now()->startOfWeek())
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $recentSales = PosSale::where('user_id', Auth::id())
            ->with('client')
            ->whereDate('sale_date', today())
            ->latest()
            ->take(10)
            ->get();

        return view('pos.daily-summary', compact('summary', 'weekSales', 'recentSales'));
    }

    /**
     * Link POS sale to invoice.
     */
    public function linkToInvoice(Request $request, PosSale $posSale)
    {
        if ($posSale->user_id !== Auth::id()) abort(403);
        if ($posSale->invoice_id) {
            return back()->with('error', 'This POS sale is already linked to an invoice.');
        }

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $clientId = $validated['client_id'] ?? $posSale->client_id;
        if (!$clientId) {
            return back()->with('error', 'A client is required to create an invoice from a POS sale.');
        }

        // Create invoice from POS sale
        $invoice = Invoice::create([
            'user_id' => Auth::id(),
            'client_id' => $clientId,
            'invoice_date' => $posSale->sale_date,
            'due_date' => $posSale->sale_date->copy()->addDays(30),
            'subtotal' => $posSale->subtotal,
            'tax_amount' => $posSale->tax_amount,
            'discount_amount' => $posSale->discount_amount,
            'total_amount' => $posSale->total_amount,
            'paid_amount' => $posSale->paid_amount,
            'balance_due' => $posSale->total_amount - $posSale->paid_amount,
            'status' => $posSale->paid_amount >= $posSale->total_amount ? 'paid' : 'pending',
            'payment_status' => $posSale->paid_amount >= $posSale->total_amount ? 'paid' : ($posSale->paid_amount > 0 ? 'partial' : 'unpaid'),
            'notes' => 'Created from POS Sale #' . $posSale->pos_number . ': ' . $posSale->notes,
            'currency' => \App\Models\Setting::forUser(Auth::id())->default_currency ?? 'UGX',
        ]);

        // Create invoice items from POS items
        foreach ($posSale->saleItems as $saleItem) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $saleItem->item_id,
                'item_name' => $saleItem->item_name,
                'unit_price' => $saleItem->unit_price,
                'quantity' => $saleItem->quantity,
                'line_total' => $saleItem->line_total,
                'tax_rate' => $saleItem->tax_rate,
                'tax_amount' => $saleItem->tax_amount,
                'discount_amount' => $saleItem->discount_amount,
                'total_amount' => $saleItem->total_amount,
            ]);
        }

        // Link POS sale to invoice
        $posSale->update([
            'invoice_id' => $invoice->id,
            'client_id' => $clientId,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'POS sale linked to invoice successfully!');
    }

    /**
     * Generate receipt PDF.
     */
    public function receipt(PosSale $posSale)
    {
        if ($posSale->user_id !== Auth::id()) abort(403);
        $posSale->load(['saleItems', 'client']);
        $settings = \App\Models\Setting::forUser($posSale->user_id);

        $pdf = app('dompdf.wrapper')->loadView('pos.receipt-pdf', [
            'sale' => $posSale,
            'settings' => $settings,
        ]);

        return $pdf->download("receipt-{$posSale->pos_number}.pdf");
    }
}
