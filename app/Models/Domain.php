<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Domain extends Model
{
    use LogsActivity;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'domain_name',
        'status',
        'registration_date',
        'expiry_date',
        'renewal_date',
        'registration_cost',
        'renewal_cost',
        'registrar',
        'provider',
        'nameservers',
        'dns_records',
        'auto_renew',
        'privacy_protection',
        'contact_email',
        'notes',
        'api_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registration_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'registration_cost' => 'decimal:2',
        'renewal_cost' => 'decimal:2',
        'auto_renew' => 'boolean',
        'privacy_protection' => 'boolean',
        'nameservers' => 'array',
        'dns_records' => 'array',
        'api_data' => 'array',
    ];

    /**
     * Get the user that owns the domain.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client associated with the domain.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope a query to only include active domains.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include expired domains.
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere('expiry_date', '<', Carbon::now());
        });
    }

    /**
     * Scope a query to only include domains expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('status', 'active');
    }

    /**
     * Check if domain is expiring soon.
     */
    public function isExpiringSoon($days = 30): bool
    {
        return $this->expiry_date <= Carbon::now()->addDays($days) && $this->status === 'active';
    }

    /**
     * Check if domain is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date < Carbon::now() || $this->status === 'expired';
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Get the domain extension.
     */
    public function getExtensionAttribute(): string
    {
        $parts = explode('.', $this->domain_name);
        return '.' . end($parts);
    }

    /**
     * Get the domain without extension.
     */
    public function getDomainWithoutExtensionAttribute(): string
    {
        $parts = explode('.', $this->domain_name);
        array_pop($parts);
        return implode('.', $parts);
    }

    /**
     * Get formatted nameservers.
     */
    public function getFormattedNameserversAttribute(): string
    {
        if (empty($this->nameservers)) {
            return 'Not set';
        }
        
        return implode(', ', $this->nameservers);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'badge-success',
            'expired' => 'badge-danger',
            'suspended' => 'badge-warning',
            'pending' => 'badge-info',
            default => 'badge-secondary'
        };
    }

    /**
     * Get nameserver by index safely.
     */
    public function getNameserver(int $index): string
    {
        if (is_array($this->nameservers) && isset($this->nameservers[$index])) {
            return $this->nameservers[$index];
        }
        return '';
    }

    /**
     * Get DNS record by index safely.
     */
    public function getDnsRecord(int $index): string
    {
        if (is_array($this->dns_records) && isset($this->dns_records[$index])) {
            return $this->dns_records[$index];
        }
        return '';
    }

    /**
     * Update domain statuses based on expiry dates.
     */
    public static function updateExpiredStatuses()
    {
        // Update domains that are past expiry date to expired status
        static::where('expiry_date', '<', Carbon::now())
              ->where('status', '!=', 'expired')
              ->update(['status' => 'expired']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
