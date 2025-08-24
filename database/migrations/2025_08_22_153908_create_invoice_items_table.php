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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('set null');
            
            // Item Information (stored at time of invoice creation)
            $table->string('item_name'); // Snapshot of item name
            $table->text('item_description')->nullable();
            $table->string('item_sku')->nullable();
            
            // Pricing Information (at time of invoice)
            $table->decimal('unit_price', 10, 2); // Price per unit at invoice time
            $table->decimal('quantity', 10, 2); // Can be fractional for services
            $table->string('unit_type')->default('piece'); // piece, hour, kg, etc.
            
            // Calculated Fields
            $table->decimal('line_total', 10, 2); // unit_price * quantity
            
            // Tax Information
            $table->boolean('is_taxable')->default(false);
            $table->decimal('tax_rate', 5, 2)->nullable(); // Tax rate at invoice time
            $table->decimal('tax_amount', 10, 2)->default(0);
            
            // Discount Information (per line item)
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 8, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            
            // Final Amount (after tax and discount)
            $table->decimal('total_amount', 10, 2); // line_total + tax_amount - discount_amount
            
            // Sort order for display
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['invoice_id', 'sort_order']);
            $table->index(['item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
