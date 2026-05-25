<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Purchase extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id', 'supplier_id', 'purchase_number', 'reference',
        'purchase_date', 'delivery_date', 'subtotal', 'tax_amount',
        'discount_amount', 'total_amount', 'paid_amount', 'balance_due',
        'status', 'payment_status', 'warehouse_id', 'notes', 'terms',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('line_total');
        $taxAmount = $this->items()->sum('tax_amount');
        $totalAmount = $subtotal + $taxAmount - ($this->discount_amount ?? 0);

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'balance_due' => $totalAmount - ($this->paid_amount ?? 0),
        ]);
    }

    public static function generatePurchaseNumber($userId): string
    {
        $settings = Setting::forUser($userId);
        $prefix = $settings->purchase_prefix ?? 'PUR';
        $number = $settings->next_purchase_number ?? 1;
        $settings->increment('next_purchase_number');
        return $prefix . '-' . date('Y') . '-' . str_pad($number, $settings->purchase_number_length ?? 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($purchase) {
            if (empty($purchase->purchase_number)) {
                $purchase->purchase_number = self::generatePurchaseNumber($purchase->user_id);
            }
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
