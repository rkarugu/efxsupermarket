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
        Schema::table('wa_customers', function (Blueprint $table) {
            $table->string('biller_number')->nullable();
            $table->string('account_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_customers', function (Blueprint $table) {
            $table->dropColumn(['biller_number', 'account_number']);
        });
    }
};
