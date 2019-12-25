<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('tweet_id');
            $table->foreign('tweet_id')
                ->references('id')
                ->on('tweets')
                ->onDelete('cascade');

            $table->dateTime('created_at');
            $table->unique(['user_id', 'tweet_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
