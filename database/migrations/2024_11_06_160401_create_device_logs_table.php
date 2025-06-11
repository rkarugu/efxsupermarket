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
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('issued_to');
            $table->unsignedBigInteger('issued_by');
            $table->unsignedBigInteger('branch_id');
            $table->dateTime('date_issued', precision: 0);
            $table->string('status')->default('Pending');
            $table->string('issue_type');
            $table->text('issued_by_comment')->nullable();
            $table->char('verify_otp',6)->nullable();
            $table->boolean('is_received')->default(false);
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};