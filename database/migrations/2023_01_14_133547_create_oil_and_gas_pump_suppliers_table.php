<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pump_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('email',255)->nullable();
            $table->string('mobile_no',20)->nullable();
            $table->string('slug',255)->unique();
            $table->unsignedBigInteger('oil_and_gas_pump_id');
            $table->double('receviable_amount', 8, 2)->default(0);
            $table->double('payable_amount', 8, 2)->default(0);
            $table->unsignedBigInteger('created_by_id');
            $table->json('note')->nullable();
            $table->json('description')->nullable();
            $table->timestamps();

            $table->unique(["name","oil_and_gas_pump_id"],'oil_and_gas_pump_suppliers_uq_1');

            $table->foreign('created_by_id','oil_and_gas_pump_suppliers_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oil_and_gas_pump_id','oil_and_gas_pump_suppliers_fk_2')->references('id')->on('oil_and_gas_pumps')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pump_suppliers');
    }
};
