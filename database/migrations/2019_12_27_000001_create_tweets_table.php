<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTweetsTable extends Migration
{
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('text');

            $table->unsignedBigInteger('author_id');
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')
                ->references('id')
                ->on('tweets')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tweets');
    }
}
