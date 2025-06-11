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
        Schema::create('device_repairs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('device_id');
            $table->string('status')->default('Repair');
            $table->integer('repair_cost')->nullable();
            $table->string('charge_to');
            $table->unsignedBigInteger('charged_user')->nullable();
            $table->text('comment')->nullable();
            $table->date('complete_date')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->text('completed_comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_repairs');
    }
};
