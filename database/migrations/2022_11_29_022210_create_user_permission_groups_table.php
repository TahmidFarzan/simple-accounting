<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_permission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('code',100)->unique();
            $table->string('slug',255)->unique();
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->foreign('created_by_id','user_permission_groups_fk_1')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_permission_groups');
    }
};
