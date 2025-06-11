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
        Schema::create('wa_petty_cash_logs', function (Blueprint $table) {
            $table->id();
            $table->json('petty_cash_transaction_ids');
            $table->string('petty_cash_type');
            $table->unsignedInteger('initiated_by');
            $table->foreign('initiated_by')->references('id')->on('users');
            $table->timestamp('initiated_time')->useCurrent();
            $table->unsignedInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->timestamp('approved_time')->nullable();
            $table->integer('transactions_count')->nullable();
            $table->double('total_amount')->nullable();
            $table->integer('successful_transactions')->nullable();
            $table->double('disbursed_amount')->nullable();
            $table->integer('failed_transactions')->nullable();
            $table->double('pending_amount')->nullable();
            $table->integer('declined_transactions')->nullable();
            $table->double('declined_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_petty_cash_logs');
    }
};
