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
        Schema::table('register_cheque', function (Blueprint $table) {
            $table->unsignedInteger('wa_customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('register_cheque', function (Blueprint $table) {
            $table->dropColumn('wa_customer_id');
        });
    }
};
