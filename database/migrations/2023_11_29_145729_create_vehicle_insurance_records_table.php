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
        Schema::create('vehicle_insurance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('insurer');
            $table->string('type')->comment('comprehensive, third_party');
            $table->double('insurance_amount');
            $table->string('insurance_period')->comment('months');
            $table->timestamp('insurance_date');
            $table->timestamp('due_date')->nullable();
            $table->integer('reminder_months')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_insurancerecords');
    }
};
