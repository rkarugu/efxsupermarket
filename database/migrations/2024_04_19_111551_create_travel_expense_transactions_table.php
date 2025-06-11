<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_expense_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type')->comment('incentive, withdrawal, deduction');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('shift_id');
            $table->string('shift_type')->comment('delivery, order_taking');
            $table->double('amount');
            $table->string('document_no');
            $table->string('wallet_type');
            $table->unsignedInteger('wallet_type_id');
            $table->string('reference');
            $table->string('narrative');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_expense_transactions');
    }
};
