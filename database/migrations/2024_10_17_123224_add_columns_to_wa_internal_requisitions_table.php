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
        Schema::table('wa_internal_requisitions', function (Blueprint $table) {
            $table->string('payment_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_internal_requisitions', function (Blueprint $table) {
            $table->dropColumn(['payment_code']);
        });
    }
};
