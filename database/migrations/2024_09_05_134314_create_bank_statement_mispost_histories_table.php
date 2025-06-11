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
        Schema::create('bank_statement_mispost_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('created_by');
            $table->string('old_channel');
            $table->string('new_channel');
            $table->date('old_bank_date');
            $table->date('new_bank_date');
            $table->unsignedInteger('payment_verification_bank_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_mispost_histories');
    }
};
