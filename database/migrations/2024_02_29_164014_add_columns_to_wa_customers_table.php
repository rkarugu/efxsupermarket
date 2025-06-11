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
            $table -> string('kcb_till')->nullable();
            $table -> string('equity_till')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_customers', function (Blueprint $table) {
            $table -> dropColumn(['kcb_till', 'equity_till']);
        });
    }
};
