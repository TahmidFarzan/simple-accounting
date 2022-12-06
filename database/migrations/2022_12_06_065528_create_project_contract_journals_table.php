<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_contract_journals', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('slug',200)->unique();
            $table->dateTime('entry_date')->nullable();
            $table->text('description')->nullable();
            $table->json('note')->nullable();
            $table->enum('entry_type', ['Revenue','Loss'])->default('Revenue');
            $table->double('amount', 8, 2)->default(0);

            $table->unsignedBigInteger('created_by_id');
            $table->unsignedBigInteger('project_contract_id');

            $table->timestamps();

            $table->foreign('created_by_id','project_contract_journals_fk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_contract_id','project_contract_journals_fk_2')->references('id')->on('project_contracts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contract_journals');
    }
};
