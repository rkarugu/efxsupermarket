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
        Schema::create('wa_close_branch_end_of_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wa_location_and_store_id');
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->dateTime('closed_date')->nullable();
            $table->dateTime('opened_date')->nullable();
            $table->dateTime('closed_time')->nullable();
            $table->dateTime('opened_time')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_close_branch_end_of_days');
    }
};
