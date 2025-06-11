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
        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->boolean('manual_upload_status')->default(false);
            $table->unsignedBigInteger('bank_statement_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->dropColumn(['manual_upload_status', 'bank_statement_id']);
        });
    }
};
