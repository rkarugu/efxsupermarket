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
        Schema::create('request_new_skus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_agreement_id');
            $table->unsignedBigInteger('wa_supplier_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('trade_agreement_reference');
            $table->string('supplier_sku_code');
            $table->string('supplier_sku_name');
            $table->unsignedBigInteger('pack_size_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->longText('trade_agreement_discount');
            $table->string('gross_weight');
            $table->decimal('price_list_cost', 12,2);
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_new_skus');
    }
};
