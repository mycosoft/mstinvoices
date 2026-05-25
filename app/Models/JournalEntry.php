<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JournalEntry extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id',
        'reference_number',
        'date',
        'description',
        'source_type',
        'source_id',
        'is_posted',
        'posted_at',
        'total_debit',
        'total_credit',
    ];

    protected $casts = [
        'date' => 'date',
        'posted_at' => 'datetime',
        'is_posted' => 'boolean',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('is_posted', false);
    }

    public function scopeBySourceType($query, $type)
    {
        return $query->where('source_type', $type);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function getIsBalancedAttribute(): bool
    {
        return bccomp($this->total_debit, $this->total_credit, 2) === 0;
    }

    public static function generateReferenceNumber($userId): string
    {
        $settings = Setting::forUser($userId);
        $prefix = 'JE';
        $lastEntry = static::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->first();
        $next = $lastEntry ? $lastEntry->id + 1 : 1;
        return $prefix . '-' . date('Y') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (empty($entry->reference_number)) {
                $entry->reference_number = self::generateReferenceNumber($entry->user_id);
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
