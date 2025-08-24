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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Invoice Identification
            $table->string('invoice_number')->unique();
            $table->string('reference_number')->nullable(); // Client reference
            
            // Invoice Dates
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('sent_date')->nullable();
            $table->date('paid_date')->nullable();
            
            // Financial Information
            $table->decimal('subtotal', 10, 2)->default(0); // Before tax and discounts
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0); // Final amount
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);
            
            // Discount Information
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 8, 2)->nullable();
            
            // Invoice Status
            $table->enum('status', ['draft', 'sent', 'viewed', 'paid', 'partial', 'overdue', 'cancelled'])->default('draft');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            
            // Additional Information
            $table->text('notes')->nullable(); // Internal notes
            $table->text('terms')->nullable(); // Payment terms
            $table->text('footer')->nullable(); // Invoice footer
            
            // Currency and Locale
            $table->string('currency', 3)->default('USD');
            $table->string('locale', 5)->default('en_US');
            
            // Tracking
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_viewed_at')->nullable();
            $table->integer('view_count')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['client_id']);
            $table->index(['invoice_date']);
            $table->index(['due_date']);
            $table->index(['status', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
