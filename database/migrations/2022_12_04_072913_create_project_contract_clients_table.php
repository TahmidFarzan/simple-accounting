<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('project_contract_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->enum('gender', ['Male','Female',"Other"])->default('Male');
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->string('mobile_no',20)->nullable()->unique();
            $table->string('email',255)->nullable()->unique();
            $table->string('slug',255)->unique();

            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by_id','project_contract_clients_agents_fk_1')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contract_clients');
    }
};
