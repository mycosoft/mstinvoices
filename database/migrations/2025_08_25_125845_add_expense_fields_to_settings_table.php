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
        Schema::table('settings', function (Blueprint $table) {
            // Expense number settings
            $table->string('expense_prefix')->default('EXP')->after('next_quotation_number');
            $table->integer('expense_number_length')->default(4)->after('expense_prefix');
            $table->integer('next_expense_number')->default(1)->after('expense_number_length');
            
            // Default expense settings
            $table->decimal('default_expense_tax_rate', 5, 2)->default(0)->after('next_expense_number');
            $table->boolean('expense_requires_approval')->default(false)->after('default_expense_tax_rate');
            $table->decimal('expense_approval_threshold', 10, 2)->nullable()->after('expense_requires_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'expense_prefix',
                'expense_number_length',
                'next_expense_number',
                'default_expense_tax_rate',
                'expense_requires_approval',
                'expense_approval_threshold',
            ]);
        });
    }
};
