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
        Schema::create('stock_debtor_tran_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('stock_debtor_trans_id');
            $table->unsignedInteger('stock_debtors_id');
            $table->unsignedInteger('inventory_item_id');
            $table->integer('quantity')->nullable();
            $table->string('document_no');
            $table->unsignedInteger('stock_moves_id')->nullable();
            $table->unsignedInteger('wa_debtor_trans_id')->nullable();
            $table->decimal('price',10,2);
            $table->decimal('vat',10,2);
            $table->decimal('vat_percentage',10,2);
            $table->decimal('discount',10,2);
            $table->decimal('discount_percentage',10,2);
            $table->decimal('total',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_debtor_tran_items');
    }
};
