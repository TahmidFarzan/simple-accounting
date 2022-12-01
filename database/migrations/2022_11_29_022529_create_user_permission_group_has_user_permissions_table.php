<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_permission_group_has_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_permission_group_id');
            $table->unsignedBigInteger('user_permission_id');
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            $table->unique(["user_permission_group_id","user_permission_id"],'user_permission_group_has_user_permissions_uq_1');

            $table->foreign('created_by_id',"user_permission_group_has_user_permissions_fk_1")->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_permission_id',"user_permission_group_has_user_permissions_fk_2")->references('id')->on('user_permissions')->onDelete('cascade');
            $table->foreign('user_permission_group_id',"user_permission_group_has_user_permissions_fk_3")->references('id')->on('user_permission_groups')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_permission_group_has_user_permissions');
    }
};
