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
        Schema::create('trade_billing_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_agreement_id');
            $table->foreign('trade_agreement_id')->references('id')->on('trade_agreements')->onDelete('cascade');

            $table->unsignedInteger('wa_currency_manager_id')->nullable();
            $table->foreign('wa_currency_manager_id')->references('id')->on('wa_currency_managers')->nullOnDelete();

            $table->decimal('charges',20,2);

            $table->string('billing_period'); //Monthly, Yearly

            $table->string('title')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_billing_plans');
    }
};
