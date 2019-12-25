<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\Poll;
use Apathy\Discuss\Models\Tweet;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Poll::class, function (Faker $faker) {
    return [
        'title' => $faker->realText(30),
        'end_datetime' => Carbon::create(2020),
        'tweet_id' => Tweet::inRandomOrder()->first()->id,
    ];
});
