<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->double('invoice_amount')->nullable()->change();
        });
    }

    public function down(): void
    {
        //
    }
};
