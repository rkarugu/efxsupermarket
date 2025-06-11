<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_withholding_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_no');
            $table->unsignedInteger('wa_bank_account_id');
            $table->unsignedInteger('prepared_by');
            $table->double('amount');
            $table->timestamps();
        });

        Schema::create('wa_withholding_file_items', function (Blueprint $table) {
            $table->id();
            $table->string('wa_withholding_file_id');
            $table->string('wa_bank_file_id');
            $table->double('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_withholding_files');
        Schema::dropIfExists('wa_withholding_file_items');
    }
};
