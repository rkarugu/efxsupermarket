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
        Schema::create('returned_grns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number');
            $table->unsignedInteger('grn_id');
            $table->string('item_code');
            $table->double('returned_quantity');
            $table->unsignedInteger('initiated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returned_grns');
    }
};
