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
        Schema::table('item_promotions', function (Blueprint $table) {
            $table->string('promotion_type_id')->nullable();
            $table->string('supplier_id')->nullable();
            $table->unsignedInteger('promotion_group_id')->nullable();
            $table->unsignedInteger('wa_demand_id')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('current_price', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('promotion_price', 10, 2)->nullable();

            $table->dropForeign(['promotion_item_id']);

            $table->integer('sale_quantity')->nullable()->change();
            $table->integer('promotion_quantity')->nullable()->change();
            $table->unsignedInteger('promotion_item_id')->nullable()->change();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_promotions', function (Blueprint $table) {
            $table->dropColumn([
                'promotion_type_id',
                'wa_demand_id',
                'promotion_group_id',
                'discount_percentage',
                'current_price',
                'discount_amount',
                'promotion_price',
                'supplier_id',
                'sale_quantity',
                'promotion_quantity'
            ]);
        });
    }
};
