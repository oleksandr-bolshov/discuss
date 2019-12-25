<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
