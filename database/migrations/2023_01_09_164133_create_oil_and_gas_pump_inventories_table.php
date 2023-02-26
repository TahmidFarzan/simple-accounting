<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pump_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('slug',255)->unique();
            $table->unsignedBigInteger('created_by_id');
            $table->unsignedBigInteger('oagp_product_id')->unique();
            $table->double('quantity')->default(0);
            $table->double('sell_price', 8, 2)->default(0);
            $table->double('purchase_price', 8, 2)->default(0);

            $table->timestamps();

            $table->foreign('created_by_id','oil_and_gas_pump_inventories_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oagp_product_id','oil_and_gas_pump_inventories_fk_2')->references('id')->on('oil_and_gas_pump_products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pump_inventories');
    }
};
