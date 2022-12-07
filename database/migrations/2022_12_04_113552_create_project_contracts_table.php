<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('code',200)->unique();
            $table->date('start_date')->useCurrent();
            $table->date('end_date')->useCurrent();
            $table->string('slug',200)->unique();
            $table->text('description')->nullable();
            $table->json('note');
            $table->enum('status', ['Ongoing','Complete'])->default('Ongoing');
            $table->double('invested_amount', 8, 2)->default(0);
            $table->enum('receivable_status', ['NotStarted','Due',"Partial","Full"])->default('NotStarted');

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->foreign('client_id','project_contracts_fk_1')->references('id')->on('project_contract_clients')->onDelete('cascade');
            $table->foreign('created_by_id','project_contracts_fk_2')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id','project_contracts_fk_3')->references('id')->on('project_contract_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contracts');
    }
};
