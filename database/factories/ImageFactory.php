<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\Image;
use Apathy\Discuss\Models\Tweet;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'path' => $faker->unique()->imageUrl(),
        'tweet_id' => Tweet::inRandomOrder()->first()->id,
    ];
});
