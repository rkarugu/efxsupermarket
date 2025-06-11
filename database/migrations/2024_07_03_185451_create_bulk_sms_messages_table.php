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
        Schema::create('bulk_sms_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bulk_sms_id')->index('idx_bulk_sms_test_messages_bulk_sms_id');
            $table->unsignedBigInteger('created_by')->index('idx_bulk_sms_test_messages_created_by');
            $table->string('issn');
            $table->string('phone_number')->index('idx_bulk_sms_test_messages_phone_number');
            $table->text('message');
            $table->string('category');
            $table->string('send_status');
            $table->string('sms_length');
            $table->string('sms_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_sms_messages');
    }
};
