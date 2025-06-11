<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_notes', function (Blueprint $table) {
            $table->after('note_no', function (Blueprint $table) {
                $table->string('cu_invoice_number');
                $table->string('supplier_invoice_number');
            });
        });
    }

    public function down(): void
    {
        Schema::table('financial_notes', function (Blueprint $table) {
            $table->dropColumn(['cu_invoice_number', 'supplier_invoice_number']);
        });
    }
};
