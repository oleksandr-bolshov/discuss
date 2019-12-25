<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
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

            $table->text('text');
            $table->boolean('is_read')->default(false);
            $table->dateTime('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
