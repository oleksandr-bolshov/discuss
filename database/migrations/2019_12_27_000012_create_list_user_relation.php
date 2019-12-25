<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListUserRelation extends Migration
{
    public function up()
    {
        Schema::create('list_user', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('list_id');
            $table->foreign('list_id')
                ->references('id')
                ->on('lists')
                ->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('user_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('list_user');
    }
}
