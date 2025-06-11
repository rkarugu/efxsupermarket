<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('returned_grns', function (Blueprint $table) {
            $table->string('grn_number')->after('grn_id');
            $table->boolean('is_printed')->default(0);
        });

        // update grn numbers in returned grns
        DB::table('returned_grns')
            ->join('wa_grns', 'wa_grns.id', '=', 'returned_grns.grn_id')
            ->update(['returned_grns.grn_number' => DB::raw('wa_grns.grn_number')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returned_grns', function (Blueprint $table) {
            $table->dropColumn(['grn_number', 'is_printed']);
        });
    }
};
