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
            $table->string('quotation_prefix', 10)->default('QUO')->after('next_invoice_number');
            $table->integer('quotation_number_length')->default(4)->after('quotation_prefix');
            $table->integer('next_quotation_number')->default(1)->after('quotation_number_length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['quotation_prefix', 'quotation_number_length', 'next_quotation_number']);
        });
    }
};
