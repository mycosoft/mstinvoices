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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('domain_name')->unique();
            $table->string('status')->default('active'); // active, expired, suspended, pending
            $table->date('registration_date');
            $table->date('expiry_date');
            $table->date('renewal_date')->nullable();
            $table->decimal('registration_cost', 10, 2)->nullable();
            $table->decimal('renewal_cost', 10, 2)->nullable();
            $table->string('registrar')->default('NameSilo');
            $table->string('nameservers')->nullable(); // JSON array of nameservers
            $table->string('dns_records')->nullable(); // JSON array of DNS records
            $table->boolean('auto_renew')->default(false);
            $table->boolean('privacy_protection')->default(false);
            $table->string('contact_email')->nullable();
            $table->text('notes')->nullable();
            $table->json('api_data')->nullable(); // Store NameSilo API response data
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['expiry_date']);
            $table->index(['domain_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
