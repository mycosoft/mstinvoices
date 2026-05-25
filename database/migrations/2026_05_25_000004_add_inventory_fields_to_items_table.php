<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('track_inventory')->default(false)->after('is_service');
            $table->integer('stock_quantity')->nullable()->after('track_inventory');
            $table->integer('low_stock_alert')->nullable()->after('stock_quantity');
            $table->string('warehouse_location')->nullable()->after('low_stock_alert');
            $table->integer('reorder_point')->nullable()->after('warehouse_location');
            $table->integer('reorder_quantity')->nullable()->after('reorder_point');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'track_inventory',
                'stock_quantity',
                'low_stock_alert',
                'warehouse_location',
                'reorder_point',
                'reorder_quantity',
            ]);
        });
    }
};
