<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('type',200);
            $table->string('code',200)->unique();
            $table->string('slug',255)->unique();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(["name","type"],'user_permissions_uq_1');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_permissions');
    }
};
