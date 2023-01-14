<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pump_purchase_items', function (Blueprint $table) {
            $table->string('slug',200)->unique();
            $table->unsignedBigInteger('oagp_purchase_id');
            $table->unsignedBigInteger('oagp_product_id');
            $table->double('count')->default(0);
            $table->double('sell_price', 8, 2)->default(0);
            $table->double('purchase_price', 8, 2)->default(0);
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->foreign('created_by_id','oil_and_gas_pump_purchase_items_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oagp_product_id','oil_and_gas_pump_purchase_items_fk_2')->references('id')->on('oil_and_gas_pump_products')->onDelete('cascade');
            $table->foreign('oagp_purchase_id','oil_and_gas_pump_purchase_items_fk_3')->references('id')->on('oil_and_gas_pump_products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pump_purchase_items');
    }
};
