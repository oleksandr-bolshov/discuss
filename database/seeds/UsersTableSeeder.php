<?php

use Apathy\Discuss\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        factory(User::class, 10)->create();
    }
}
