<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pump_sell_payments', function (Blueprint $table) {
            $table->id();
            $table->json('note');
            $table->string('slug',200)->unique();
            $table->unsignedBigInteger('oagp_sell_id');
            $table->double('amount', 8, 2)->default(0);
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->foreign('created_by_id','oil_and_gas_pump_sell_payments_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oagp_sell_id','oil_and_gas_pump_sell_payments_fk_2')->references('id')->on('oil_and_gas_pump_sells')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pump_sell_payments');
    }
};
