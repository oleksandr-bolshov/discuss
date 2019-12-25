<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\Chat;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Chat::class, function (Faker $faker) {
    return [
        'created_at' => Carbon::now(),
    ];
});
