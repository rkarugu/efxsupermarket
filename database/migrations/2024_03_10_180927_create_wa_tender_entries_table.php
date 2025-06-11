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
        Schema::create('wa_tender_entries', function (Blueprint $table) {
            $table->id();
            $table->string('document_no');
            $table->string('account_code');
            $table->string('reference');
            $table->date('trans_date');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('wa_payment_method_id');
            $table->unsignedInteger('cashier_id');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_tender_entries');
    }
};
