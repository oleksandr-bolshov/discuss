<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Tweet::class, function (Faker $faker) {
    return [
        'text' => $faker->text(),
        'author_id' => User::inRandomOrder()->first()->id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
