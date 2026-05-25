<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code', 20);
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->string('subtype')->nullable();
            $table->text('description')->nullable();
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->boolean('is_system')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->foreignId('parent_account_id')->nullable()->constrained('accounts')->onDelete('set null');

            $table->timestamps();

            $table->unique(['user_id', 'code']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
