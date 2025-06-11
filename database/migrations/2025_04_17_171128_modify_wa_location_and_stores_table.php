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
        Schema::table('wa_location_and_stores', function (Blueprint $table) {
            $table->string('location_code')->nullable();
            $table->string('location_name')->nullable();
            $table->decimal('credit_limit', 10, 2)->nullable();
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('wa_branch_id')->nullable();
            $table->boolean('is_cost_centre')->default(false);
            $table->string('account_no')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->string('biller_no')->nullable();
            $table->boolean('is_physical_store')->default(false);
            $table->string('phone_number')->nullable()->after('biller_no');
            $table->string('email')->nullable()->after('phone_number');
            $table->text('address')->nullable()->after('email');
            $table->string('contact_person')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_location_and_stores', function (Blueprint $table) {
            $table->dropColumn([
                'location_code',
                'location_name',
                'credit_limit',
                'slug',
                'wa_branch_id',
                'is_cost_centre',
                'account_no',
                'route_id',
                'biller_no',
                'is_physical_store',
                'phone_number',
                'email',
                'address',
                'contact_person'
            ]);
        });
    }
};
