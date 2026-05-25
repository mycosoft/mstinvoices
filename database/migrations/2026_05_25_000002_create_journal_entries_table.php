<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference_number');
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('source_type')->nullable(); // pos_sale, purchase, invoice_payment, expense, manual, etc.
            $table->unsignedBigInteger('source_id')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->datetime('posted_at')->nullable();
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);

            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['source_type', 'source_id']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
