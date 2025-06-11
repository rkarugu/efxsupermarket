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
        Schema::create('wallet_trans', function (Blueprint $table) {
            $table->integer('employee_id');
            $table->string('transaction_type')->comment('withdrawal', 'deposit', 'incentive');
            $table->string('amount');
            $table->integer('route_id')->nullable();
            $table->integer('shift_id')->nullable();
            $table->integer('delivery_schedule_id')->nullable();
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_trans');
    }
};
