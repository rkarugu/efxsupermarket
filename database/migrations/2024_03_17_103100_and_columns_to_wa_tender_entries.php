<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_tender_entries', function (Blueprint $table) {
            $table->string('channel')->after('wa_payment_method_id')->nullable();
            $table->string('paid_by')->after('channel')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('wa_tender_entries', function (Blueprint $table) {
            $table->dropColumn('channel');
            $table->dropColumn('paid_by');
        });
    }
};
