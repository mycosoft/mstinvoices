<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Account extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id',
        'code',
        'name',
        'type',
        'subtype',
        'description',
        'normal_balance',
        'is_system',
        'status',
        'opening_balance',
        'current_balance',
        'parent_account_id',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_system' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAsset($query)
    {
        return $query->where('type', 'asset');
    }

    public function scopeLiability($query)
    {
        return $query->where('type', 'liability');
    }

    public function scopeEquity($query)
    {
        return $query->where('type', 'equity');
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function getFormattedBalanceAttribute(): string
    {
        $settings = Setting::forUser($this->user_id);
        return $settings->formatCurrency(abs($this->current_balance));
    }

    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    public function updateBalance(float $debit, float $credit): void
    {
        if ($this->normal_balance === 'debit') {
            $this->current_balance += ($debit - $credit);
        } else {
            $this->current_balance += ($credit - $debit);
        }
        $this->save();
    }

    public static function getAccountTypes(): array
    {
        return [
            'asset' => 'Asset',
            'liability' => 'Liability',
            'equity' => 'Equity',
            'income' => 'Income',
            'expense' => 'Expense',
        ];
    }

    public static function getNormalBalanceForType(string $type): string
    {
        return in_array($type, ['asset', 'expense']) ? 'debit' : 'credit';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
