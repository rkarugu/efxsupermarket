<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('petty_cash_transactions', function (Blueprint $table) {
            $table->string('initial_approval_status')->default('pending');
            $table->string('final_approval_status')->default('pending');
            $table->unsignedInteger('initial_approved_by')->nullable();
            $table->timestamp('initial_approval_time')->nullable();
            $table->unsignedInteger('final_approved_by')->nullable();
            $table->timestamp('final_approval_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petty_cash_transactions', function (Blueprint $table) {
            $table->dropColumn(['initial_approval_status', 'final_approval_status', 'initial_approved_by', 'initial_approval_time', 'final_approved_by', 'final_approval_time']);
        });
    }
};
