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
        Schema::create('salesman_reporting_customer_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_route_customer_id');
            $table->unsignedBigInteger('salesman_shift_id');
            $table->string('code');
            $table->foreign('wa_route_customer_id')->on('wa_route_customers')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('salesman_shift_id')->on('salesman_shifts')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salesman_reporting_customer_codes');
    }
};
