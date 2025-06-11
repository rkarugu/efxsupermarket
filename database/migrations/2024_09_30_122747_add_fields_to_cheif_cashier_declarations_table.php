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
        Schema::table('chief_cashier_declarations', function (Blueprint $table) {
            $table->unsignedBigInteger('wa_close_branch_end_of_day_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chief_cashier_declarations', function (Blueprint $table) {
            $table->dropColumn('wa_close_branch_end_of_day_id');
        });
    }
};
