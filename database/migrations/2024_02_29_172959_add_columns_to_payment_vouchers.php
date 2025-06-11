<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->string('number')->after('id');
            $table->string('document_number')->after('number')->nullable();
            $table->string('narration')->nullable()->after('wa_payment_method_id');
        });
    }

    public function down(): void
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropColumn('number');
            $table->dropColumn('document_number');
            $table->dropColumn('narration');
        });
    }
};
