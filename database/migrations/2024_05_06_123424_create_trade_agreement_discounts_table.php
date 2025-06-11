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
        Schema::create('trade_agreement_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_agreements_id')->nullable();
            $table->foreign('trade_agreements_id')->references('id')->on('trade_agreements')->onDelete('cascade');
            $table->string('discount_type');
            $table->string('discount_name')->nullable();
            $table->decimal('discount_value')->nullable()->default(0);
            $table->boolean('applies_to_all_item')->nullable()->default(false);
            $table->decimal('purchased_product_quantity')->nullable()->default(0);
            $table->decimal('free_product_quantity')->nullable()->default(0);
            $table->json("other_options")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_agreement_discounts');
    }
};
