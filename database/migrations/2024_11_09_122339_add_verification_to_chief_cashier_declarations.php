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
        Schema::table('chief_cashier_declarations', function (Blueprint $table) {
            $table->boolean('verified')->default(false);
            $table->boolean('manual_allocation')->default(false);
            $table->unsignedBigInteger('bank_statement_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chief_cashier_declarations', function (Blueprint $table) {
            $table->dropColumn(['verified', 'manual_allocation', 'bank_statement_id']);
        });
    }
};
