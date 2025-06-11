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
        Schema::table('wa_pos_cash_sales_items_return', function (Blueprint $table) {
            $table->string('reason')->nullable();
            $table->boolean('is_late')->default(false);
            $table->boolean('is_over_limit')->default(false);
            $table->boolean('late_approval_accepted')->default(false);
            $table->boolean('over_limit_approval_accepted')->default(false);
            $table->unsignedInteger('late_approval_by')->nullable();
            $table->timestamp('late_approval_date')->nullable();
            $table->unsignedInteger('over_limit_approval_by')->nullable();
            $table->unsignedInteger('reason_id')->nullable();
            $table->timestamp('over_limit_approval_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_items_return', function (Blueprint $table) {
            $table->dropColumn([
                'reason','is_late',
                'late_approval_by',
                'late_approval_date',
                'over_limit_approval_date',
                'over_limit_approval_by',
                'is_over_limit',
                'reason_id',
                'late_approval_accepted',
                'over_limit_approval_accepted',
            ]);
        });
    }
};
