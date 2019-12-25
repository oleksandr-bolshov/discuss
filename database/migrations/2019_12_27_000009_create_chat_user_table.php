<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatUserTable extends Migration
{
    public function up()
    {
        Schema::create('chat_user', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('chat_id');
            $table->foreign('chat_id')
                ->references('id')
                ->on('chats')
                ->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->dateTime('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_user');
    }
}
