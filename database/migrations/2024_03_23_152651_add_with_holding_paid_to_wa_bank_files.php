<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_bank_files', function (Blueprint $table) {
            $table->boolean('withholding_paid')->default(0)->after('prepared_by');
        });
    }

    public function down(): void
    {
        Schema::table('wa_bank_files', function (Blueprint $table) {
            $table->dropColumn('withholding_paid');
        });
    }
};
