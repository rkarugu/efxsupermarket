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
        Schema::create('gl_posting_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->string('transaction_no');
            $table->string('document_no');
            $table->decimal('amount',10,2);
            $table->unsignedBigInteger('wa_banktrans_id');
            $table->unsignedBigInteger('wa_debtor_trans_id');
            $table->unsignedBigInteger('payment_verification_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_posting_logs');
    }
};
