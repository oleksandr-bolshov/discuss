<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\Like;
use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Like::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'tweet_id' => Tweet::inRandomOrder()->first()->id,
        'created_at' => Carbon::now(),
    ];
});
