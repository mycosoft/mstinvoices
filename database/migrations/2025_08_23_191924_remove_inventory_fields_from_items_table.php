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
        Schema::table('items', function (Blueprint $table) {
            // Remove inventory tracking fields
            $table->dropColumn(['track_inventory', 'stock_quantity', 'low_stock_alert']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Add back inventory tracking fields
            $table->boolean('track_inventory')->default(false)->after('tax_rate');
            $table->integer('stock_quantity')->nullable()->after('track_inventory');
            $table->integer('low_stock_alert')->nullable()->after('stock_quantity');
        });
    }
};
