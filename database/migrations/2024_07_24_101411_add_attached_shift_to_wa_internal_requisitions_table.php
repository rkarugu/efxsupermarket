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
            $table->integer('attached_shift_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_internal_requisitions', function (Blueprint $table) {
            $table->dropColumn('attached_shift_id');
        });
    }
};
