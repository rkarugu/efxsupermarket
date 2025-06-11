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
        Schema::create('casuals_pay_disbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('casuals_pay_period_detail_id');
            $table->string('document_no');
            $table->double('amount');
            $table->string('reference')->nullable();
            $table->longText('narrative')->nullable();
            $table->string('call_back_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casuals_pay_disbursements');
    }
};
