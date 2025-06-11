<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_purchase_order_id');
            $table->unsignedInteger('wa_supp_tran_id');
            $table->string('grn_number');
            $table->date('grn_date');
            $table->string('invoice_number');
            $table->string('supplier_invoice_number')->nullable();
            $table->string('cu_invoice_number')->nullable();
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('prepared_by');
            $table->double('discount_amount');
            $table->double('vat_amount');
            $table->double('amount');
            $table->timestamps();
        });

        Schema::create('wa_supplier_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_supplier_invoice_id');
            $table->string('code');
            $table->string('description');
            $table->double('quantity');
            $table->double('standart_cost_unit');
            $table->double('discount_amount');
            $table->double('vat_amount');
            $table->double('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_supplier_invoices');
        Schema::dropIfExists('wa_supplier_invoice_items');
    }
};
