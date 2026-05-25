<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class InvoiceItem extends Model
{
    use LogsActivity;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'item_id',
        'item_name',
        'item_description',
        'item_sku',
        'unit_price',
        'quantity',
        'unit_type',
        'line_total',
        'is_taxable',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_taxable' => 'boolean',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Get the invoice that owns the invoice item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the item that this invoice item references.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }

    /**
     * Get the formatted line total.
     */
    public function getFormattedLineTotalAttribute(): string
    {
        return '$' . number_format($this->line_total, 2);
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    /**
     * Calculate line total, tax, and final amount.
     */
    public function calculateAmounts(): void
    {
        // Calculate line total
        $lineTotal = $this->unit_price * $this->quantity;
        
        // Calculate discount
        $discountAmount = 0;
        if ($this->discount_type && $this->discount_value) {
            if ($this->discount_type === 'percentage') {
                $discountAmount = $lineTotal * ($this->discount_value / 100);
            } else {
                $discountAmount = $this->discount_value;
            }
        }
        
        // Calculate tax
        $taxAmount = 0;
        if ($this->is_taxable && $this->tax_rate) {
            $taxableAmount = $lineTotal - $discountAmount;
            $taxAmount = $taxableAmount * ($this->tax_rate / 100);
        }
        
        // Calculate final amount
        $totalAmount = $lineTotal - $discountAmount + $taxAmount;
        
        $this->line_total = $lineTotal;
        $this->discount_amount = $discountAmount;
        $this->tax_amount = $taxAmount;
        $this->total_amount = $totalAmount;
    }

    /**
     * Create invoice item from item model.
     */
    public static function createFromItem(Invoice $invoice, Item $item, float $quantity = 1, int $sortOrder = 0): self
    {
        $invoiceItem = new self([
            'invoice_id' => $invoice->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'item_description' => $item->description,
            'item_sku' => $item->sku,
            'unit_price' => $item->unit_price,
            'quantity' => $quantity,
            'unit_type' => $item->unit_type,
            'is_taxable' => $item->is_taxable,
            'tax_rate' => $item->tax_rate,
            'sort_order' => $sortOrder,
        ]);
        
        $invoiceItem->calculateAmounts();
        
        return $invoiceItem;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($invoiceItem) {
            $invoiceItem->calculateAmounts();
        });
        
        static::saved(function ($invoiceItem) {
            // Recalculate invoice totals when item is saved
            $invoiceItem->invoice->calculateTotals();
        });
        
        static::deleted(function ($invoiceItem) {
            // Recalculate invoice totals when item is deleted
            $invoiceItem->invoice->calculateTotals();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
