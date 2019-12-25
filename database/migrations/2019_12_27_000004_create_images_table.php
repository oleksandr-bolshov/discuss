<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path');

            $table->unsignedBigInteger('tweet_id');
            $table->foreign('tweet_id')
                ->references('id')
                ->on('tweets')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
    }
}
