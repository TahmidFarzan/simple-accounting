<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_has_user_permission_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_permission_group_id');
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->foreign('user_id',"user_has_user_permission_groups_fk_1")->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_permission_group_id',"user_has_user_permission_groups_fk_2")->references('id')->on('user_permission_groups')->onDelete('cascade');
            $table->foreign('created_by_id',"user_has_user_permission_groups_fk_3")->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_has_user_permission_groups');
    }
};
