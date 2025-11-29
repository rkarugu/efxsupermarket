<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToWaRouteCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->nullable()->after('status')->comment('Customer pricing category: 1=Distribution, 2=Wholesale, 3=Retail');
            
            // Add foreign key if wa_categories table exists
            // $table->foreign('category_id')->references('id')->on('wa_categories')->onDelete('set null');
        });
        
        // Set default category to Retail (3) for existing customers
        DB::table('wa_route_customers')->whereNull('category_id')->update(['category_id' => 3]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            // Drop foreign key if it exists
            // $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
