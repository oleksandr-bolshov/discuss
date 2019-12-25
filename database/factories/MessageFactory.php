<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\Chat;
use Apathy\Discuss\Models\Message;
use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
        'chat_id' => Chat::inRandomOrder()->first()->id,
        'user_id' => User::inRandomOrder()->first()->id,
        'text' => $faker->realText(100),
        'is_read' => true,
        'created_at' => Carbon::now()->subDays(rand(2, 200)),
    ];
});
