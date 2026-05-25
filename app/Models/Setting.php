<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Setting extends Model
{
    use LogsActivity;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        
        // Company Information
        'company_name',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'company_city',
        'company_state',
        'company_postal_code',
        'company_country',
        'company_tax_number',
        'company_registration_number',
        'company_logo_path',
        
        // Invoice Settings
        'default_currency',
        'currency_symbol',
        'currency_position',
        'currency_decimal_places',
        'invoice_prefix',
        'invoice_number_length',
        'next_invoice_number',
        'quotation_prefix',
        'quotation_number_length',
        'next_quotation_number',
        'default_payment_terms',
        'default_tax_rate',
        'default_invoice_notes',
        'default_terms_conditions',
        'default_invoice_footer',
        
        // Expense Settings
        'expense_prefix',
        'expense_number_length',
        'next_expense_number',
        'default_expense_tax_rate',
        'expense_requires_approval',
        'expense_approval_threshold',
        
        // Email Settings
        'email_from_name',
        'email_from_address',
        'email_reply_to',
        'email_invoice_subject',
        'email_invoice_body',
        
        // System Settings
        'date_format',
        'time_format',
        'timezone',
        'language',
        'auto_send_invoices',
        'auto_reminder_enabled',
        'reminder_days_before',
        'reminder_days_after',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_tax_rate' => 'decimal:2',
        'currency_decimal_places' => 'integer',
        'auto_send_invoices' => 'boolean',
        'auto_reminder_enabled' => 'boolean',
        'next_invoice_number' => 'integer',
        'invoice_number_length' => 'integer',
        'next_quotation_number' => 'integer',
        'quotation_number_length' => 'integer',
        'next_expense_number' => 'integer',
        'expense_number_length' => 'integer',
        'default_expense_tax_rate' => 'decimal:2',
        'expense_requires_approval' => 'boolean',
        'expense_approval_threshold' => 'decimal:2',
        'default_payment_terms' => 'integer',
        'reminder_days_before' => 'integer',
        'reminder_days_after' => 'integer',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get settings for the current user or create default ones.
     */
    public static function forUser($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        return static::where('user_id', $userId)->first() ?? static::create(array_merge(
            ['user_id' => $userId],
            static::getDefaultSettings()
        ));
    }

    /**
     * Get default settings values.
     */
    public static function getDefaultSettings()
    {
        return [
            'company_name' => 'Your Company Name',
            'default_currency' => 'UGX',
            'currency_symbol' => 'UGX',
            'currency_position' => 'before',
            'currency_decimal_places' => 0,
            'invoice_prefix' => 'INV',
            'invoice_number_length' => 4,
            'next_invoice_number' => 1,
            'quotation_prefix' => 'QUO',
            'quotation_number_length' => 4,
            'next_quotation_number' => 1,
            'expense_prefix' => 'EXP',
            'expense_number_length' => 4,
            'next_expense_number' => 1,
            'default_expense_tax_rate' => 0.00,
            'expense_requires_approval' => false,
            'expense_approval_threshold' => null,
            'default_payment_terms' => 30,
            'default_tax_rate' => 0.00,
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'timezone' => 'UTC',
            'language' => 'en',
            'auto_send_invoices' => false,
            'auto_reminder_enabled' => false,
            'reminder_days_before' => 3,
            'reminder_days_after' => 7,
        ];
    }

    /**
     * Get the full company address.
     */
    public function getFullCompanyAddressAttribute()
    {
        $parts = array_filter([
            $this->company_address,
            $this->company_city,
            $this->company_state,
            $this->company_postal_code,
            $this->company_country,
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get the full URL for the company logo.
     */
    public function getCompanyLogoUrlAttribute()
    {
        if ($this->company_logo_path) {
            return asset('storage/' . $this->company_logo_path);
        }
        return null;
    }

    /**
     * Format currency amount.
     */
    public function formatCurrency($amount)
    {
        $decimalPlaces = $this->currency_decimal_places ?? 0;
        $formatted = number_format($amount, $decimalPlaces);
        
        if ($this->currency_position === 'before') {
            return $this->currency_symbol . $formatted;
        }
        
        return $formatted . $this->currency_symbol;
    }

    /**
     * Format currency amount with extra space for PDF/Preview display.
     */
    public function formatCurrencyWithBreak($amount)
    {
        $decimalPlaces = $this->currency_decimal_places ?? 0;
        $formatted = number_format($amount, $decimalPlaces);
        
        if ($this->currency_position === 'before') {
            return $this->currency_symbol . '&nbsp;' . $formatted;
        }
        
        return $formatted . '&nbsp;' . $this->currency_symbol;
    }

    /**
     * Format item description by converting hyphens to line breaks.
     */
    public static function formatDescription($description)
    {
        if (!$description) {
            return '';
        }
        
        // Replace ' - ' or ' - ' with '<br>' for line breaks
        $formatted = preg_replace('/\s*-\s*/', '<br>', $description);
        
        return $formatted;
    }

    /**
     * Get the next invoice number and increment it.
     */
    public function getNextInvoiceNumber()
    {
        $number = $this->next_invoice_number;
        $this->increment('next_invoice_number');
        
        return $this->invoice_prefix . '-' . date('Y') . '-' . str_pad($number, $this->invoice_number_length, '0', STR_PAD_LEFT);
    }

    /**
     * Get the next quotation number and increment it.
     */
    public function getNextQuotationNumber()
    {
        $number = $this->next_quotation_number;
        $this->increment('next_quotation_number');
        
        return $this->quotation_prefix . '-' . date('Y') . '-' . str_pad($number, $this->quotation_number_length, '0', STR_PAD_LEFT);
    }

    /**
     * Get the next expense number and increment it.
     */
    public function getNextExpenseNumber()
    {
        $number = $this->next_expense_number;
        $this->increment('next_expense_number');
        
        return $this->expense_prefix . '-' . date('Y') . '-' . str_pad($number, $this->expense_number_length, '0', STR_PAD_LEFT);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['company_name', 'default_currency', 'tax_enabled', 'tax_rate', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
