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
        Schema::create('wa_debtor_tran_recons', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->index();
            $table->string('date');
            $table->string('document_no');
            $table->string('reference');
            $table->string('channel')->nullable();
            $table->string('user');
            $table->double('total');
            $table->boolean('flagged');
            $table->string('reason')->nullable();
            $table->boolean('batch');
            $table->integer('count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_debtor_tran_recons');
    }
};
