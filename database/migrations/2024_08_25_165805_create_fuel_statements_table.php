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
        Schema::create('fuel_statements', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->string('receipt_number');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('matched_fuel_entry_id')->nullable();
            $table->unsignedBigInteger('verification_record_id')->nullable();
            $table->double('quantity');
            $table->double('terminal_price');
            $table->double('discount');
            $table->string('narrative');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_statements');
    }
};
