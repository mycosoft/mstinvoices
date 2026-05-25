<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Invoice extends Model
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
        'invoice_number',
        'reference_number',
        'invoice_date',
        'due_date',
        'sent_date',
        'paid_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'discount_type',
        'discount_value',
        'status',
        'payment_status',
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
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_date' => 'date',
        'paid_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'view_count' => 'integer',
        'last_sent_at' => 'datetime',
        'last_viewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client that the invoice belongs to.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the invoice items for the invoice.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class)->orderBy('payment_date', 'desc');
    }

    /**
     * Scope a query to only include invoices for a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include invoices by payment status.
     */
    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                      ->whereIn('payment_status', ['unpaid', 'partial']);
    }

    /**
     * Scope a query to only include invoices due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                      ->whereIn('payment_status', ['unpaid', 'partial']);
    }

    /**
     * Check if the invoice is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && 
               in_array($this->payment_status, ['unpaid', 'partial']);
    }

    /**
     * Get the number of days until due (negative if overdue).
     */
    public function getDaysUntilDueAttribute(): int
    {
        return today()->diffInDays($this->due_date, false);
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total_amount, 2);
    }

    /**
     * Get the formatted balance due.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->balance_due, 2);
    }

    /**
     * Get the payment progress percentage.
     */
    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_amount == 0) {
            return 100;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    /**
     * Add a payment to the invoice (partial or full)
     */
    public function addPayment($amount, $paymentMethod = 'cash', $notes = null): InvoicePayment
    {
        // Create payment record
        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $paymentMethod,
            'notes' => $notes,
            'created_by' => auth()->id()
        ]);

        // Update invoice payment status
        $this->paid_amount += $amount;
        $this->balance_due = max(0, $this->total_amount - $this->paid_amount);
        
        // Determine payment status
        if ($this->balance_due <= 0) {
            $this->payment_status = 'paid';
            $this->paid_date = now();
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partial';
            $this->status = 'partial';
        }
        
        $this->save();
        
        return $payment;
    }

    /**
     * Get total payments made this month
     */
    public function getPaymentsThisMonthAttribute(): float
    {
        $thisMonth = now()->startOfMonth();
        return $this->payments()
            ->where('payment_date', '>=', $thisMonth)
            ->sum('amount');
    }

    /**
     * Get the latest payment
     */
    public function getLatestPaymentAttribute(): ?InvoicePayment
    {
        return $this->payments()->latest('payment_date')->first();
    }

    /**
     * Get payment history summary
     */
    public function getPaymentHistoryAttribute(): array
    {
        $payments = $this->payments()->orderBy('payment_date', 'desc')->get();
        
        return [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'payment_methods' => $payments->pluck('payment_method')->unique()->values(),
            'last_payment_date' => $payments->first()?->payment_date,
            'payment_progress' => $this->payment_progress
        ];
    }

    /**
     * Generate the next invoice number.
     */
    public static function generateInvoiceNumber($userId): string
    {
        $settings = Setting::forUser($userId);
        return $settings->getNextInvoiceNumber();
    }

    /**
     * Calculate and update invoice totals.
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->invoiceItems()->sum('line_total');
        $taxAmount = $this->invoiceItems()->sum('tax_amount');
        $itemDiscounts = $this->invoiceItems()->sum('discount_amount');
        
        // Apply invoice-level discount
        $invoiceDiscount = 0;
        if ($this->discount_type && $this->discount_value) {
            if ($this->discount_type === 'percentage') {
                $invoiceDiscount = $subtotal * ($this->discount_value / 100);
            } else {
                $invoiceDiscount = $this->discount_value;
            }
        }
        
        $totalDiscount = $itemDiscounts + $invoiceDiscount;
        $totalAmount = $subtotal + $taxAmount - $totalDiscount;
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $totalDiscount,
            'total_amount' => $totalAmount,
            'balance_due' => $totalAmount - ($this->paid_amount ?? 0),
        ]);
    }

    /**
     * Mark invoice as sent.
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
     * Mark invoice as viewed.
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
     * Recalculate payment status based on actual payments
     */
    public function recalculatePaymentStatus(): void
    {
        $totalPayments = $this->payments()->sum('amount');
        
        $this->paid_amount = $totalPayments;
        $this->balance_due = max(0, $this->total_amount - $totalPayments);
        
        if ($this->balance_due <= 0) {
            $this->payment_status = 'paid';
            $this->paid_date = $this->payments()->latest('payment_date')->first()?->payment_date;
            $this->status = 'paid';
        } elseif ($totalPayments > 0) {
            $this->payment_status = 'partial';
            $this->status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
            $this->status = 'pending';
            $this->paid_date = null;
        }
        
        $this->save();
    }



    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber($invoice->user_id);
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
