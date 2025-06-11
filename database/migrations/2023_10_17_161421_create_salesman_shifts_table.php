<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesmanShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesman_shifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('salesman_id');
            $table->foreign('salesman_id')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedInteger('route_id');
            $table->string('shift_type');
            $table->string('status')->default('open')->comment('open, close, dispatched');
            $table->string('closed_time')->nullable();
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
        Schema::dropIfExists('salesman_shifts');
    }
}
