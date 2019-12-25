<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Models\UserList;
use Faker\Generator as Faker;

$factory->define(UserList::class, function (Faker $faker) {
    return [
        'title' => $faker->realText(25),
        'owner_id' => User::inRandomOrder()->first()->id,
    ];
});
