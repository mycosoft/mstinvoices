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
            // Drop the index first if it exists
            $table->dropIndex(['sku']);
            // Remove the SKU column
            $table->dropColumn('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Add back the SKU column
            $table->string('sku')->nullable()->after('description');
            // Add back the index
            $table->index('sku');
        });
    }
};
