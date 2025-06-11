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
        Schema::create('casuals_pay_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('branch_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('open'); // open, closed
            $table->boolean('initial_approval')->default(false);
            $table->unsignedInteger('initial_approver')->nullable();
            $table->timestamp('initial_approval_date')->nullable();
            $table->boolean('final_approval')->default(false);
            $table->unsignedInteger('final_approver')->nullable();
            $table->timestamp('final_approval_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casuals_pay_periods');
    }
};
