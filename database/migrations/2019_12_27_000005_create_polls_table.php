<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->dateTime('end_datetime');

            $table->unsignedBigInteger('tweet_id');
            $table->foreign('tweet_id')
                ->references('id')
                ->on('tweets')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('polls');
    }
}
