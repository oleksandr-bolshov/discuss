<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowingsTable extends Migration
{
    public function up()
    {
        Schema::create('followings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('follower_id');
            $table->foreign('follower_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->dateTime('created_at');
            $table->unique(['user_id', 'follower_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('followings');
    }
}
