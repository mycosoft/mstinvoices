<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PosSale extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id', 'client_id', 'invoice_id', 'pos_number', 'sale_date',
        'subtotal', 'tax_amount', 'discount_amount', 'total_amount',
        'paid_amount', 'change_amount', 'payment_method', 'status',
        'notes', 'cashier_name',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(PosSaleItem::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public static function generatePosNumber($userId): string
    {
        $settings = Setting::forUser($userId);
        $prefix = $settings->pos_prefix ?? 'POS';
        $number = $settings->next_pos_number ?? 1;
        $settings->increment('next_pos_number');
        return $prefix . '-' . date('Ymd') . '-' . str_pad($number, $settings->pos_number_length ?? 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($sale) {
            if (empty($sale->pos_number)) {
                $sale->pos_number = self::generatePosNumber($sale->user_id);
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
