<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('poll_option_id');
            $table->foreign('poll_option_id')
                ->references('id')
                ->on('poll_options')
                ->onDelete('cascade');

            $table->dateTime('created_at');
            $table->unique(['user_id', 'poll_option_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
}
