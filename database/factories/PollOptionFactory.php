<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\PollOption;
use Faker\Generator as Faker;

$factory->define(PollOption::class, function (Faker $faker) {
    return [
        'option' => $faker->realText(20),
        'poll_id' => 1,
    ];
});
