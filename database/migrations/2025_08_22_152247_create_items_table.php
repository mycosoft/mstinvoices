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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic Information
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->nullable(); // Stock Keeping Unit
            $table->string('category')->nullable();
            
            // Pricing Information
            $table->decimal('unit_price', 10, 2); // Price per unit
            $table->decimal('cost_price', 10, 2)->nullable(); // Cost for profit calculation
            $table->string('unit_type')->default('piece'); // piece, hour, kg, etc.
            
            // Tax Information
            $table->boolean('is_taxable')->default(true);
            $table->decimal('tax_rate', 5, 2)->nullable(); // Tax percentage
            
            // Inventory Information
            $table->boolean('track_inventory')->default(false);
            $table->integer('stock_quantity')->nullable();
            $table->integer('low_stock_alert')->nullable();
            
            // Status and Visibility
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_service')->default(false); // Product vs Service
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->string('image_url')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
