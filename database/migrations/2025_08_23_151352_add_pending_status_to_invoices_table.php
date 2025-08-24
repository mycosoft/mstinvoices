<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use raw SQL to modify ENUM
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft', 'pending', 'sent', 'viewed', 'paid', 'partial', 'overdue', 'cancelled') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM without 'pending'
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft', 'sent', 'viewed', 'paid', 'partial', 'overdue', 'cancelled') DEFAULT 'draft'");
    }
};
