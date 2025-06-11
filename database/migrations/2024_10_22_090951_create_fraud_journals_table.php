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
        Schema::create('fraud_journals', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number');
            $table->timestamp('reference_date');
            $table->string('document_no');
            $table->string('document_reference');
            $table->unsignedBigInteger('customer_account_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('posted_by');
            $table->text('narrative');
            $table->text('comments');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_journals');
    }
};
