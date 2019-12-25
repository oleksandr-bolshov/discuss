<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('poll_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('poll_id');
            $table->foreign('poll_id')
                ->references('id')
                ->on('polls')
                ->onDelete('cascade');
            $table->string('option', 25);
        });
    }

    public function down()
    {
        Schema::dropIfExists('poll_options');
    }
}
