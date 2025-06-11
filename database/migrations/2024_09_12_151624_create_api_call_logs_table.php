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
        Schema::create('api_call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('lpo_number')->nullable();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->string('status')->nullable()->default('pending'); // 'pending', 'success', 'failed'
            $table->text('request_data')->nullable(); // Request data
            $table->text('response_data')->nullable(); // Response from the Supplier Portal
            $table->text('error_message')->nullable(); // Any error messages
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_call_logs');
    }
};
