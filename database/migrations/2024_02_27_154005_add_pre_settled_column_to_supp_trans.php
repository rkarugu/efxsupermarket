<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_supp_trans', function (Blueprint $table) {
            $table->boolean('pre_settled')->default(0)->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('wa_supp_trans', function (Blueprint $table) {
            $table->dropColumn('pre_settled');
        });
    }
};
