<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockMovement extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id',
        'item_id',
        'warehouse_id',
        'type',
        'quantity',
        'unit_cost',
        'reference_type',
        'reference_id',
        'notes',
        'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'movement_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('movement_date', [$from, $to]);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            'transfer' => 'Transfer',
            'return' => 'Return',
            default => ucfirst($this->type),
        };
    }

    public static function recordMovement(Item $item, string $type, float $quantity, ?float $unitCost = null, ?string $referenceType = null, ?int $referenceId = null, ?string $notes = null, ?int $warehouseId = null): self
    {
        $movement = self::create([
            'user_id' => $item->user_id,
            'item_id' => $item->id,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'quantity' => $quantity,
            'unit_cost' => $unitCost ?? $item->cost_price,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'movement_date' => now()->toDateString(),
        ]);

        // Update item stock
        if ($item->track_inventory) {
            if (in_array($type, ['in', 'return'])) {
                $item->increment('stock_quantity', $quantity);
            } elseif (in_array($type, ['out'])) {
                $item->decrement('stock_quantity', $quantity);
            } elseif ($type === 'adjustment') {
                $item->update(['stock_quantity' => $quantity]);
            }
        }

        return $movement;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
