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
        Schema::table('banked_drop_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
        });
        Schema::table('banked_cash_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
        });
        Schema::table('crc_records', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
