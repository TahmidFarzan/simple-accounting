<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_contract_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name',200)->unique();
            $table->string('slug',255)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by_id','project_contract_payment_methods_fk_1')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contract_payment_methods');
    }
};
