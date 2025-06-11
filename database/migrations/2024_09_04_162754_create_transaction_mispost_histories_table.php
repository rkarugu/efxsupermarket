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
        Schema::create('transaction_mispost_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('created_by');
            $table->string('old_channel');
            $table->string('new_channel');
            $table->unsignedInteger('wa_debtor_trans_id');
            $table->unsignedInteger('wa_customer_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_mispost_histories');
    }
};
