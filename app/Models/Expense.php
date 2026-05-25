<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Expense extends Model
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
        'expense_number',
        'reference_number',
        'title',
        'description',
        'category',
        'vendor_name',
        'vendor_email',
        'vendor_phone',
        'amount',
        'tax_amount',
        'net_amount',
        'tax_rate',
        'expense_date',
        'due_date',
        'paid_date',
        'status',
        'payment_status',
        'payment_method',
        'requires_approval',
        'approved_by',
        'approved_at',
        'approval_notes',
        'currency',
        'locale',
        'notes',
        'is_billable',
        'is_reimbursable',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
        'attachments',
        'receipt_number',
        'project_code',
        'department',
        'location',
        'tags',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'approved_at' => 'datetime',
        'recurring_end_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_billable' => 'boolean',
        'is_reimbursable' => 'boolean',
        'is_recurring' => 'boolean',
        'attachments' => 'array',
        'tags' => 'array',
    ];

    /**
     * Get the user that owns the expense.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client associated with the expense (optional).
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
     * Get the user who approved the expense.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include expenses for a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include expenses by payment status.
     */
    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Scope a query to only include expenses by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include overdue expenses.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                      ->where('payment_status', '!=', 'paid')
                      ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope a query to only include expenses due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                      ->where('payment_status', '!=', 'paid')
                      ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope a query to only include billable expenses.
     */
    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    /**
     * Scope a query to only include reimbursable expenses.
     */
    public function scopeReimbursable($query)
    {
        return $query->where('is_reimbursable', true);
    }

    /**
     * Scope a query to only include pending approval expenses.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('requires_approval', true)
                      ->where('status', 'pending')
                      ->whereNull('approved_by');
    }

    /**
     * Scope a query to only include approved expenses.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')
                      ->whereNotNull('approved_by');
    }

    /**
     * Scope a query to only include recurring expenses.
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Check if the expense is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->payment_status !== 'paid' &&
               $this->status !== 'cancelled';
    }

    /**
     * Get the number of days until due (negative if overdue).
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }
        return today()->diffInDays($this->due_date, false);
    }

    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get the formatted net amount.
     */
    public function getFormattedNetAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->net_amount, 2);
    }

    /**
     * Get the formatted tax amount.
     */
    public function getFormattedTaxAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->tax_amount, 2);
    }

    /**
     * Check if expense requires approval.
     */
    public function getRequiresApprovalStatusAttribute(): bool
    {
        return $this->requires_approval && !$this->approved_by;
    }

    /**
     * Check if expense is approved.
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->approved_by !== null;
    }

    /**
     * Generate the next expense number.
     */
    public static function generateExpenseNumber($userId): string
    {
        $settings = Setting::forUser($userId);
        return $settings->getNextExpenseNumber();
    }

    /**
     * Calculate tax amount based on net amount and tax rate.
     */
    public function calculateTax(): void
    {
        if ($this->tax_rate > 0) {
            $this->tax_amount = $this->net_amount * ($this->tax_rate / 100);
            $this->amount = $this->net_amount + $this->tax_amount;
        } else {
            $this->tax_amount = 0;
            $this->amount = $this->net_amount;
        }
    }

    /**
     * Approve the expense.
     */
    public function approve($approverId, $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Reject the expense.
     */
    public function reject($approverId, $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Mark expense as paid.
     */
    public function markAsPaid($paymentMethod = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $paymentMethod,
            'status' => $this->status === 'approved' ? 'paid' : $this->status,
        ]);
    }

    /**
     * Get common expense categories.
     */
    public static function getCategories(): array
    {
        return [
            'Office Supplies',
            'Travel & Accommodation',
            'Meals & Entertainment',
            'Transportation',
            'Marketing & Advertising',
            'Professional Services',
            'Software & Subscriptions',
            'Insurance',
            'Rent & Utilities',
            'Equipment & Maintenance',
            'Training & Development',
            'Telecommunications',
            'Banking & Financial',
            'Legal & Compliance',
            'Research & Development',
            'Other',
        ];
    }

    /**
     * Get payment methods.
     */
    public static function getPaymentMethods(): array
    {
        return [
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'paypal' => 'PayPal',
            'other' => 'Other',
        ];
    }

    /**
     * Get status options.
     */
    public static function getStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get payment status options.
     */
    public static function getPaymentStatuses(): array
    {
        return [
            'unpaid' => 'Unpaid',
            'paid' => 'Paid',
            'partial' => 'Partial',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($expense) {
            if (empty($expense->expense_number)) {
                $expense->expense_number = self::generateExpenseNumber($expense->user_id);
            }
            
            // Calculate tax if not set
            if ($expense->net_amount && $expense->tax_rate) {
                $expense->calculateTax();
            } elseif ($expense->amount && !$expense->net_amount) {
                $expense->net_amount = $expense->amount;
            }
        });
        
        static::updating(function ($expense) {
            // Recalculate tax if amounts or rates changed
            if ($expense->isDirty(['net_amount', 'tax_rate'])) {
                $expense->calculateTax();
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
