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
        Schema::table('wa_charts_of_accounts', function (Blueprint $table) {
            $table->integer('wa_account_sub_section_id')->index();
            $table->unsignedInteger('parent_id')->index();
            $table->tinyInteger('is_parent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_charts_of_accounts', function (Blueprint $table) {
            $table->dropColumn(['wa_account_sub_section_id', 'parent_id']);
        });
    }
};
