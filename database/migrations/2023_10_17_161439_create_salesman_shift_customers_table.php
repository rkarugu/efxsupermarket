<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesmanShiftCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesman_shift_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('salesman_shift_id');
            $table->foreign('salesman_shift_id')->references('id')->on('salesman_shifts')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('route_customer_id');
            $table->unsignedSmallInteger('visited')->default(0);
            $table->string('salesman_shift_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salesman_shift_customers');
    }
}
