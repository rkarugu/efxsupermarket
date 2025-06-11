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
        Schema::table('wa_petty_cash_requests', function (Blueprint $table) {
            $table->boolean('rejected')->default(false)->after('final_approval_date');
            $table->string('rejected_stage')->nullable()->after('rejected');
            $table->unsignedInteger('rejected_by')->nullable()->after('rejected_stage');
            $table->foreign('rejected_by')->references('id')->on('users');
            $table->timestamp('rejected_date')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_requests', function (Blueprint $table) {
            $table->dropColumn('rejected');
            $table->dropColumn('rejected_stage');
            $table->dropConstrainedForeignId('rejected_by');
            $table->dropColumn('rejected_date');
        });
    }
};
