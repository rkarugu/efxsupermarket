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
        Schema::table('pos_cash_payments', function (Blueprint $table) {
            $table->string('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->string('disbursed_by')->nullable();
            $table->string('disbursed_at')->nullable();
            $table->integer('print_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_cash_payments', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'rejected_at', 'rejection_reason', 'disbursed_by', 'disbursed_at', 'print_count']);
        });
    }
};
