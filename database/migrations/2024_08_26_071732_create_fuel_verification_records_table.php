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
        Schema::create('fuel_verification_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->timestamp('verification_date');
            $table->timestamp('fueling_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_verification_records');
    }
};
