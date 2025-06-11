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
        Schema::create('casuals_pay_period_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('casuals_pay_period_id');
            $table->unsignedBigInteger('casual_id');
            $table->json('dates');
            $table->double('amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casuals_pay_period_details');
    }
};
