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
        Schema::create('suspended_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_customer_id');
            $table->unsignedInteger('edited_wa_customer_id');
            $table->unsignedInteger('suspended_by');
            $table->unsignedInteger('resolved_by');
            $table->string('document_no')->index();
            $table->string('reference');
            $table->string('edited_reference');
            $table->double('amount');
            $table->double('edited_amount');
            $table->string('trans_date');
            $table->string('input_date');
            $table->string('route');
            $table->string('reason');
            $table->string('status')->default('suspended');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspended_transactions');
    }
};
