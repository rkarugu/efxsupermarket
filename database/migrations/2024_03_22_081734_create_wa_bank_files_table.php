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
        Schema::create('wa_bank_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_no');
            $table->unsignedInteger('wa_bank_account_id');
            $table->unsignedInteger('prepared_by');
            $table->double('amount');
            $table->timestamps();
        });

        Schema::create('wa_bank_file_items', function (Blueprint $table) {
            $table->id();
            $table->string('wa_bank_file_id');
            $table->string('payment_voucher_id');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_bank_files');
        Schema::dropIfExists('wa_bank_file_items');
    }
};
