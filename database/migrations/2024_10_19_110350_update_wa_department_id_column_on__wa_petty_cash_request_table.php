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
            $table->unsignedInteger('wa_department_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_petty_cash_requests', function (Blueprint $table) {
            $table->unsignedInteger('wa_department_id')->nullable(false)->change();
        });
    }
};
