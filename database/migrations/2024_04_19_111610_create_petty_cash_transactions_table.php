<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->double('amount');
            $table->string('document_no');
            $table->string('wallet_type');
            $table->unsignedInteger('wallet_type_id');
            $table->unsignedInteger('parent_id');
            $table->string('reference');
            $table->string('narrative');
            $table->string('call_back_status')->default('pending');
            $table->string('recon_status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_transactions');
    }
};
