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
        Schema::create('operation_shift_check_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operation_shift_check_id');
            $table->string('detail_name');
            $table->string('detail_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_shift_check_details');
    }
};
