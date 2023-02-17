<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pump_sells', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->unsignedBigInteger('oil_and_gas_pump_id');
            $table->string('customer')->nullable();
            $table->text('customer_info')->nullable();
            $table->string('slug',200)->unique();
            $table->enum('status', ['Due','Complete'])->default('Due');
            $table->string('invoice',200);
            $table->text('description')->nullable();
            $table->json('note')->nullable();
            $table->double('discount', 8, 2)->default(0);
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->unique(["invoice","oil_and_gas_pump_id"],'oil_and_gas_pump_sells_uq_1');
            $table->unique(["name","oil_and_gas_pump_id"],'oil_and_gas_pump_sells_uq_2');

            $table->foreign('created_by_id','oil_and_gas_pump_sells_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oil_and_gas_pump_id','oil_and_gas_pump_sells_fk_2')->references('id')->on('oil_and_gas_pumps')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pump_sells');
    }
};
