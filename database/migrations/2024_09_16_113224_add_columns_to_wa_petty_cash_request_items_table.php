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
        Schema::table('wa_petty_cash_request_items', function (Blueprint $table) {
            $table->boolean('expunged')->default(false)->after('payment_reason');
            $table->unsignedInteger('expunged_by')->nullable()->index('expunged_by_index')->after('expunged');
            $table->timestamp('expunged_at')->nullable()->after('expunged_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_request_items', function (Blueprint $table) {
            $table->dropColumn('expunged', 'expunged_by', 'expunged_at');
        });
    }
};
