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
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->enum('status', array('DRAFT', 'UNAPPROVED', 'PENDING', 'PROCESSING', 'APPROVED', 'DECLINED', 'PRELPO', 'COMPLETED'))
                ->default('UNAPPROVED')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->enum('status', array('UNAPPROVED', 'PENDING', 'PROCESSING', 'APPROVED', 'DECLINED', 'PRELPO', 'COMPLETED'))
                ->default('UNAPPROVED')->change();
        });
    }
};
