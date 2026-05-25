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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('client_id')->constrained('projects')->nullOnDelete();
            $table->index(['project_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('client_id')->constrained('projects')->nullOnDelete();
            $table->index(['project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
            $table->dropIndex(['project_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
            $table->dropIndex(['project_id']);
        });
    }
};



