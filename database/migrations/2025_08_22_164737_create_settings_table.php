<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Company Information
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_website')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_postal_code')->nullable();
            $table->string('company_country')->nullable();
            $table->string('company_tax_number')->nullable();
            $table->string('company_registration_number')->nullable();
            $table->string('company_logo_path')->nullable();
            
            // Invoice Settings
            $table->string('default_currency', 3)->default('UGX');
            $table->string('currency_symbol', 10)->default('UGX');
            $table->enum('currency_position', ['before', 'after'])->default('before');
            $table->string('invoice_prefix')->default('INV');
            $table->integer('invoice_number_length')->default(4);
            $table->integer('next_invoice_number')->default(1);
            $table->integer('default_payment_terms')->default(30); // days
            $table->decimal('default_tax_rate', 5, 2)->default(0.00);
            $table->text('default_invoice_notes')->nullable();
            $table->text('default_terms_conditions')->nullable();
            $table->text('default_invoice_footer')->nullable();
            
            // Email Settings
            $table->string('email_from_name')->nullable();
            $table->string('email_from_address')->nullable();
            $table->string('email_reply_to')->nullable();
            $table->text('email_invoice_subject')->nullable();
            $table->text('email_invoice_body')->nullable();
            
            // System Settings
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('en');
            $table->boolean('auto_send_invoices')->default(false);
            $table->boolean('auto_reminder_enabled')->default(false);
            $table->integer('reminder_days_before')->default(3);
            $table->integer('reminder_days_after')->default(7);
            
            $table->timestamps();
            
            // Ensure one setting per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
