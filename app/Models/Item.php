<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Item extends Model
{
    use LogsActivity;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'unit_price',
        'cost_price',
        'unit_type',
        'is_taxable',
        'tax_rate',
        'status',
        'is_service',
        'track_inventory',
        'stock_quantity',
        'low_stock_alert',
        'warehouse_location',
        'reorder_point',
        'reorder_quantity',
        'notes',
        'image_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_service' => 'boolean',
        'track_inventory' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_alert' => 'integer',
        'reorder_point' => 'integer',
        'reorder_quantity' => 'integer',
    ];

    /**
     * Get the user that owns the item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoice items for this item.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeLowStock($query)
    {
        return $query->where('track_inventory', true)
                     ->whereRaw('stock_quantity <= low_stock_alert');
    }

    /**
     * Scope a query to only include active items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include products (not services).
     */
    public function scopeProducts($query)
    {
        return $query->where('is_service', false);
    }

    /**
     * Scope a query to only include services.
     */
    public function scopeServices($query)
    {
        return $query->where('is_service', true);
    }

    /**
     * Scope a query to only include items by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        $settings = Setting::forUser($this->user_id);
        return $settings->formatCurrency($this->unit_price);
    }

    /**
     * Get the formatted cost price.
     */
    public function getFormattedCostPriceAttribute()
    {
        if (!$this->cost_price) {
            return 'N/A';
        }
        $settings = Setting::forUser($this->user_id);
        return $settings->formatCurrency($this->cost_price);
    }

    /**
     * Get the profit margin percentage.
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return null;
        }
        
        $profit = $this->unit_price - $this->cost_price;
        return round(($profit / $this->cost_price) * 100, 2);
    }

    /**
     * Calculate price including tax.
     */
    public function getPriceWithTaxAttribute()
    {
        if (!$this->is_taxable || !$this->tax_rate) {
            return $this->unit_price;
        }
        
        return $this->unit_price + ($this->unit_price * ($this->tax_rate / 100));
    }

    /**
     * Get the formatted price including tax.
     */
    public function getFormattedPriceWithTaxAttribute()
    {
        $settings = Setting::forUser($this->user_id);
        return $settings->formatCurrency($this->price_with_tax);
    }

    /**
     * Get the item type display name.
     */
    public function getTypeDisplayAttribute()
    {
        return $this->is_service ? 'Service' : 'Product';
    }

    /**
     * Get available categories for the user.
     */
    public static function getCategoriesForUser($userId)
    {
        return self::where('user_id', $userId)
                  ->whereNotNull('category')
                  ->distinct()
                  ->pluck('category')
                  ->sort()
                  ->values();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'category', 'unit_price', 'cost_price', 'status', 'stock_quantity'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
