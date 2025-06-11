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
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->string('return_document_no')->nullable()->after('wa_supplier_id');
            $table->boolean('processed')->default(0)->after('demand_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_return_demands', function (Blueprint $table) {
            $table->dropColumn('return_document_no');
            $table->dropColumn('processed');
        });
    }
};
