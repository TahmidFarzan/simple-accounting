<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('mobile_no',15)->nullable()->unique();
            $table->string('email',255)->unique();
            $table->enum('user_role', ['Owner','Subordinate'])->default('Subordinate');
            $table->string('slug',255)->unique();
            $table->boolean('default_password')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('created_by_id');
            $table->softDeletes();
            $table->timestamps();
            $table->rememberToken();
            $table->foreign('created_by_id','users_fk_1')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
