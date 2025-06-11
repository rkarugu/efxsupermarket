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
        Schema::create('salesman_reporting_reason_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporting_reason_id');
            $table->string('reason_option');
            $table->string('data_type');
            $table->string('reason_option_key_name');
            $table->foreign('reporting_reason_id')->on('salesman_reporting_reasons')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salesman_reporting_reason_options');
    }
};
