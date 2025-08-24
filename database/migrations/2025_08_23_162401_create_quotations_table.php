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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('quotation_number')->unique();
            $table->string('reference_number')->nullable();
            $table->date('quotation_date');
            $table->date('valid_until');
            $table->datetime('sent_date')->nullable();
            $table->datetime('accepted_date')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 8, 2)->nullable();
            $table->enum('status', ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('locale', 5)->default('en_US');
            $table->datetime('last_sent_at')->nullable();
            $table->datetime('last_viewed_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'quotation_date']);
            $table->index(['user_id', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
