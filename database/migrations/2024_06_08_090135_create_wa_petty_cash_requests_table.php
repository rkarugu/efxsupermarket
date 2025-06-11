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
        Schema::create('wa_petty_cash_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->unsignedInteger('wa_department_id');
            $table->foreign('wa_department_id')->references('id')->on('wa_departments');
            $table->unsignedInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedInteger('wa_charts_of_account_id');
            $table->foreign('wa_charts_of_account_id')->references('id')->on('wa_charts_of_accounts');
            $table->string('petty_cash_no');
            $table->string('type');
            $table->boolean('initial_approval')->default(false);
            $table->unsignedInteger('initial_approver')->nullable();
            $table->foreign('initial_approver')->references('id')->on('users');
            $table->timestamp('initial_approval_date')->nullable();
            $table->boolean('final_approval')->default(false);
            $table->unsignedInteger('final_approver')->nullable();
            $table->foreign('final_approver')->references('id')->on('users');
            $table->timestamp('final_approval_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_petty_cash_requests');
    }
};
