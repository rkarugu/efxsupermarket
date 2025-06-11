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
        Schema::create('invoice_payment_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wa_internal_requisition_id');
            $table->bigInteger('payment_id');
            $table->bigInteger('payment_channel_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payment_records');
    }
};
