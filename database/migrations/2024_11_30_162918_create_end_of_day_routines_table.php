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
        Schema::create('end_of_day_routines', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->string('routine_no');
            $table->integer('branch_id');
            $table->boolean('returns_passed')->default(false);
            $table->boolean('splits_passed')->default(false);
            $table->boolean('unbalanced_transactions_passed')->default(false);
            $table->boolean('number_series_passed')->default(false);
            $table->boolean('pos_cash_at_hand_passed')->default(false);
            $table->decimal('system_cah')->nullable();
            $table->decimal('cashier_cah')->nullable();
            $table->string('closed_at')->nullable();
            $table->string('status')->default('Pending')->comment('Passed');
            $table->boolean('lock_users')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('end_of_day_routines');
    }
};
