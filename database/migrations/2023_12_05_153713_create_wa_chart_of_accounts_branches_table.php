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
        Schema::create('wa_chart_of_accounts_branches', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('wa_chart_of_account_id')->index();
            $table->integer('restaurant_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_chart_of_accounts_branches');
    }
};
