<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_bill_categories', function (Blueprint $table) {
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedInteger('gltag_id')->nullable();
            $table->string('item_bill_no')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
        });
        Schema::table('wa_expense_categories', function (Blueprint $table) {
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedInteger('gltag_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('wa_bill_categories', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('gltag_id');
            $table->dropColumn('item_bill_no');
            $table->dropColumn('branch_id');
        });
        Schema::table('wa_expense_categories', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('gltag_id');
            $table->dropColumn('branch_id');
        });
    }
};
