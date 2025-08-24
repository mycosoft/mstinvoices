<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class Quotation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'quotation_number',
        'reference_number',
        'quotation_date',
        'valid_until',
        'sent_date',
        'accepted_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'discount_type',
        'discount_value',
        'status',
        'notes',
        'terms',
        'footer',
        'currency',
        'locale',
        'last_sent_at',
        'last_viewed_at',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'sent_date' => 'date',
        'accepted_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'view_count' => 'integer',
        'last_sent_at' => 'datetime',
        'last_viewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the quotation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client that the quotation belongs to.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the quotation items for the quotation.
     */
    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    /**
     * Scope a query to only include quotations for a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include expired quotations.
     */
    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now())
                      ->whereNotIn('status', ['accepted', 'rejected', 'converted']);
    }

    /**
     * Scope a query to only include quotations expiring today.
     */
    public function scopeExpiringToday($query)
    {
        return $query->whereDate('valid_until', today())
                      ->whereNotIn('status', ['accepted', 'rejected', 'converted']);
    }

    /**
     * Check if the quotation is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until->isPast() && 
               !in_array($this->status, ['accepted', 'rejected', 'converted']);
    }

    /**
     * Get the number of days until expiry (negative if expired).
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        return today()->diffInDays($this->valid_until, false);
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total_amount, 2);
    }

    /**
     * Generate the next quotation number.
     */
    public static function generateQuotationNumber($userId): string
    {
        $settings = Setting::forUser($userId);
        return $settings->getNextQuotationNumber();
    }

    /**
     * Calculate and update quotation totals.
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->quotationItems()->sum('line_total');
        $taxAmount = $this->quotationItems()->sum('tax_amount');
        $itemDiscounts = $this->quotationItems()->sum('discount_amount');
        
        // Apply quotation-level discount
        $quotationDiscount = 0;
        if ($this->discount_type && $this->discount_value) {
            if ($this->discount_type === 'percentage') {
                $quotationDiscount = $subtotal * ($this->discount_value / 100);
            } else {
                $quotationDiscount = $this->discount_value;
            }
        }
        
        $totalDiscount = $itemDiscounts + $quotationDiscount;
        $totalAmount = $subtotal + $taxAmount - $totalDiscount;
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $totalDiscount,
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Mark quotation as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_date' => now(),
            'last_sent_at' => now(),
        ]);
    }

    /**
     * Mark quotation as viewed.
     */
    public function markAsViewed(): void
    {
        $this->increment('view_count');
        $this->update([
            'last_viewed_at' => now(),
        ]);
        
        if ($this->status === 'sent') {
            $this->update(['status' => 'viewed']);
        }
    }

    /**
     * Mark quotation as accepted.
     */
    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_date' => now(),
        ]);
    }

    /**
     * Mark quotation as rejected.
     */
    public function markAsRejected(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Convert quotation to invoice.
     */
    public function convertToInvoice(): Invoice
    {
        $settings = Setting::forUser($this->user_id);
        
        $invoice = Invoice::create([
            'user_id' => $this->user_id,
            'client_id' => $this->client_id,
            'invoice_number' => $settings->getNextInvoiceNumber(),
            'reference_number' => 'From Quotation: ' . $this->quotation_number,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30), // Default 30 days
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'notes' => $this->notes,
            'terms' => $this->terms,
            'footer' => $this->footer,
            'currency' => $this->currency,
            'locale' => $this->locale,
            'balance_due' => $this->total_amount,
            'paid_amount' => 0,
        ]);

        // Copy quotation items to invoice items
        foreach ($this->quotationItems as $quotationItem) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $quotationItem->item_id,
                'item_name' => $quotationItem->item_name,
                'item_description' => $quotationItem->item_description,
                'item_sku' => $quotationItem->item_sku,
                'unit_price' => $quotationItem->unit_price,
                'quantity' => $quotationItem->quantity,
                'unit_type' => $quotationItem->unit_type,
                'line_total' => $quotationItem->line_total,
                'is_taxable' => $quotationItem->is_taxable,
                'tax_rate' => $quotationItem->tax_rate,
                'tax_amount' => $quotationItem->tax_amount,
                'discount_type' => $quotationItem->discount_type,
                'discount_value' => $quotationItem->discount_value,
                'discount_amount' => $quotationItem->discount_amount,
                'total_amount' => $quotationItem->total_amount,
                'sort_order' => $quotationItem->sort_order,
            ]);
        }

        // Mark quotation as converted
        $this->update(['status' => 'converted']);

        return $invoice;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($quotation) {
            if (empty($quotation->quotation_number)) {
                $quotation->quotation_number = self::generateQuotationNumber($quotation->user_id);
            }
        });
    }
}
