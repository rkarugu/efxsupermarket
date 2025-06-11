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
        Schema::table('wallet_supplier_document_processes', function (Blueprint $table) {
            $table->string('approve_status')->default('Initial')->after('uploaded_date');
            $table->string('bank')->nullable()->after('approve_status');
            $table->string('payment_method')->nullable()->after('bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_supplier_document_processes', function (Blueprint $table) {
            $table->dropColumn('approve_status');
            $table->dropColumn('bank');
            $table->dropColumn('payment_method');
        });
    }
};
