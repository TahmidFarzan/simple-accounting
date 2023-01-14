<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pump_purchases', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('name',200);
            $table->string('slug',200)->unique();
            $table->string('invoice',200)->unique();
            $table->string('email',255)->nullable();
            $table->string('mobile_no',20)->nullable();
            $table->text('description')->nullable();
            $table->json('note')->nullable();
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->foreign('created_by_id','oil_and_gas_pump_inventories_fk_1')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pump_purchases');
    }
};
