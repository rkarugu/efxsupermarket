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
        Schema::table('banking_approvals', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id');
            $table->unsignedTinyInteger('sales_type')->comment('1 = Counter, 2 = Route');
            $table->longText('failure_message')->nullable();
            $table->boolean('verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedInteger('stage')->default(1)->comment('1 = Verification, 2 = Approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banking_approvals', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'sales_type']);
        });
    }
};
