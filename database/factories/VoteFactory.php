<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\PollOption;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Models\Vote;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Vote::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'poll_option_id' => PollOption::inRandomOrder()->first()->id,
        'created_at' => Carbon::now(),
    ];
});
