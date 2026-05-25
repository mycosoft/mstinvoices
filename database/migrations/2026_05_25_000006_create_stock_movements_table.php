<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['in', 'out', 'adjustment', 'transfer', 'return']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->string('reference_type')->nullable(); // purchase, pos_sale, adjustment, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->date('movement_date');

            $table->timestamps();

            $table->index(['user_id', 'item_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
