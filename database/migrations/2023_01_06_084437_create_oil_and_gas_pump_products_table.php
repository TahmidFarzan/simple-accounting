<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pump_products', function (Blueprint $table) {
            $table->id();
            $table->string('name',200)->unique();
            $table->string('slug',255)->unique();
            $table->unsignedBigInteger('oil_and_gas_pump_id');
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->unique(["name","oil_and_gas_pump_id"],'oil_and_gas_pump_products_uq_1');

            $table->foreign('created_by_id','oil_and_gas_pump_products_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oil_and_gas_pump_id','oil_and_gas_pump_products_fk_2')->references('id')->on('oil_and_gas_pumps')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pump_products');
    }
};
