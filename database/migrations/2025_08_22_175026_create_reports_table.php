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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Report Basic Information
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['revenue', 'client', 'monthly', 'yearly', 'custom', 'invoice_summary', 'payment_summary', 'tax_summary']);
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            
            // Report Configuration
            $table->json('filters')->nullable(); // Store filter criteria as JSON
            $table->json('columns')->nullable(); // Store selected columns as JSON
            $table->enum('format', ['table', 'chart', 'summary'])->default('table');
            $table->enum('chart_type', ['bar', 'line', 'pie', 'doughnut', 'area'])->nullable();
            
            // Date Range Configuration
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->enum('date_range_type', ['custom', 'today', 'yesterday', 'this_week', 'last_week', 'this_month', 'last_month', 'this_quarter', 'last_quarter', 'this_year', 'last_year'])->default('this_month');
            
            // Scheduling Configuration
            $table->boolean('is_scheduled')->default(false);
            $table->enum('schedule_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->nullable();
            $table->integer('schedule_day')->nullable(); // Day of week/month
            $table->time('schedule_time')->nullable();
            $table->timestamp('last_generated_at')->nullable();
            $table->timestamp('next_generation_at')->nullable();
            
            // Report Results Storage
            $table->json('last_result')->nullable(); // Store last generated report data
            $table->integer('generation_count')->default(0);
            $table->boolean('auto_email')->default(false);
            $table->json('email_recipients')->nullable(); // Store email list as JSON
            
            // Export Configuration
            $table->json('export_formats')->nullable(); // ['pdf', 'excel', 'csv']
            $table->string('report_file_path')->nullable(); // Path to last generated report file
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['is_scheduled', 'next_generation_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
