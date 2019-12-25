<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\Following;
use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Following::class, function (Faker $faker) {
    $usersIds = User::inRandomOrder()->take(2)->get()->pluck('id');

    return [
        'user_id' => $usersIds[0],
        'follower_id' => $usersIds[1],
        'created_at' => Carbon::now(),
    ];
});
