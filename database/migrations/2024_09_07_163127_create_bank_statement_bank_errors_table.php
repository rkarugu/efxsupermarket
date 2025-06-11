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
        Schema::create('bank_statement_bank_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('payment_verification_bank_id');
            $table->text('reason');
            $table->string('status');
            $table->unsignedInteger('restored_by')->nullable();
            $table->date('restored_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_bank_errors');
    }
};
