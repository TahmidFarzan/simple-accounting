<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_contract_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('code',200)->unique();
            $table->string('slug',255)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('created_by_id');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(["name","parent_id"],'project_contract_categories_uq_1');
            $table->foreign('created_by_id','project_contract_categories_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id','project_contract_categories_fk_2')->references('id')->on('project_contract_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contract_categories');
    }
};
