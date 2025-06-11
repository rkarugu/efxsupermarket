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
        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->string('trans_ref')->nullable();
            $table->string('type')->default('credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->dropColumn(['trans_ref', 'type']);
        });
    }
};
