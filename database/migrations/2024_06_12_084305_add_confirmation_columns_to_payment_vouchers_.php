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
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->after('prepared_by', function (Blueprint $table) {
                $table->unsignedBigInteger('confirmed_by')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->unsignedBigInteger('confirmation_approval_by')->nullable();
                $table->timestamp('confirmation_approval_at')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropColumn('confirmed_by');
            $table->dropColumn('confirmed_at');
            $table->dropColumn('confirmation_approval_by');
            $table->dropColumn('confirmation_approval_at');
        });
    }
};
