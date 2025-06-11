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
        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_verification_id')->nullable()->change();
            $table->string('original_reference')->nullable()->after('reference');
        });

        Schema::table('payment_verifications', function (Blueprint $table) {
            $table->string('channel',125)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->dropColumn('original_reference');
        });
    }
};
