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
        Schema::table('suspended_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('verification_record_id')->nullable();
            $table->string('manual_upload_status')->nullable();
            $table->unsignedBigInteger('manual_upload_approved_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suspended_transactions', function (Blueprint $table) {
            $table->dropColumn(['verification_record_id','manual_upload_status','manual_upload_approved_by']);
        });
    }
};
