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
        Schema::create('gl_reconciles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('bank_account_id');
            $table->decimal('beginning_balance',20);
            $table->decimal('ending_balance',20);
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_reconciles');
    }
};
