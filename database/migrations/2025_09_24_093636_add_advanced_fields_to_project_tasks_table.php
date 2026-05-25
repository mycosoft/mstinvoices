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
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
            $table->decimal('estimated_hours', 8, 2)->nullable()->after('priority');
            $table->decimal('actual_hours', 8, 2)->nullable()->after('estimated_hours');
            $table->date('start_date')->nullable()->after('actual_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn(['priority', 'estimated_hours', 'actual_hours', 'start_date']);
        });
    }
};
