<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the settings form.
     */
    public function index()
    {
        $settings = Setting::forUser();
        
        // Debug: Check if settings are fresh
        $settings = $settings->fresh();
        
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Company Information
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_website' => 'nullable|url|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_city' => 'nullable|string|max:100',
            'company_state' => 'nullable|string|max:100',
            'company_postal_code' => 'nullable|string|max:20',
            'company_country' => 'nullable|string|max:100',
            'company_tax_number' => 'nullable|string|max:100',
            'company_registration_number' => 'nullable|string|max:100',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Invoice Settings
            'default_currency' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'currency_position' => 'required|in:before,after',
            'invoice_prefix' => 'required|string|max:10',
            'invoice_number_length' => 'required|integer|min:3|max:10',
            'default_payment_terms' => 'required|integer|min:1|max:365',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'default_invoice_notes' => 'nullable|string|max:1000',
            'default_terms_conditions' => 'nullable|string|max:2000',
            'default_invoice_footer' => 'nullable|string|max:500',
            
            // Email Settings
            'email_from_name' => 'nullable|string|max:255',
            'email_from_address' => 'nullable|email|max:255',
            'email_reply_to' => 'nullable|email|max:255',
            'email_invoice_subject' => 'nullable|string|max:255',
            'email_invoice_body' => 'nullable|string|max:2000',
            
            // System Settings
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
            'timezone' => 'required|string|max:50',
            'language' => 'nullable|string|size:2',
            'auto_send_invoices' => 'boolean',
            'auto_reminder_enabled' => 'boolean',
            'reminder_days_before' => 'nullable|integer|min:0|max:30',
            'reminder_days_after' => 'nullable|integer|min:0|max:90',
        ]);
        
        $settings = Setting::forUser();
        
        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($settings->company_logo_path && Storage::disk('public')->exists($settings->company_logo_path)) {
                Storage::disk('public')->delete($settings->company_logo_path);
            }
            
            // Store new logo
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            $validated['company_logo_path'] = $logoPath;
        }
        
        // Remove logo field from validated data
        unset($validated['company_logo']);
        
        // Convert boolean fields properly
        $validated['auto_send_invoices'] = $request->has('auto_send_invoices') ? true : false;
        $validated['auto_reminder_enabled'] = $request->has('auto_reminder_enabled') ? true : false;
        
        // Add default language if not provided
        if (!isset($validated['language']) || empty($validated['language'])) {
            $validated['language'] = 'en';
        }
        
        // Update the settings using mass assignment
        $settings->update($validated);
        
        return redirect()->route('settings.index')
                        ->with('success', 'Settings updated successfully!');
    }

    /**
     * Remove the company logo.
     */
    public function removeLogo()
    {
        $settings = Setting::forUser();
        
        if ($settings->company_logo_path && Storage::disk('public')->exists($settings->company_logo_path)) {
            Storage::disk('public')->delete($settings->company_logo_path);
        }
        
        $settings->update(['company_logo_path' => null]);
        
        return redirect()->route('settings.index')
                        ->with('success', 'Company logo removed successfully!');
    }
}
