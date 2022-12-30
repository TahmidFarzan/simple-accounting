<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oil_and_gas_pumps', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('code',200)->unique();
            $table->string('slug',255)->unique();
            $table->text('description')->nullable();
            $table->json('note')->nullable();
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->foreign('created_by_id','oil_and_gas_pumps_fk_1')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oil_and_gas_pumps');
    }
};
