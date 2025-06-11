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
        Schema::table('cash_drop_transactions', function (Blueprint $table) {
            $table->string('reason')->nullable();
            $table->string('bank_receipt_number')->nullable();
            $table->string('reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_drop_transactions', function (Blueprint $table) {
            $table->dropColumn('reason','bank_receipt_number','reference');
        });
    }
};
