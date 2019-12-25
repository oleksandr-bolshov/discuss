<?php

use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TweetsTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        // create 2 tweets for each fake user
        $tweets = $users->map(
            function (User $user) {
                return factory(Tweet::class, 3)->make([
                    'author_id' => $user->id,
                    'parent_id' => null,
                ]);
            }
        );

        // insert all data in one db query to speed up performance a bit
        DB::table('tweets')->insert($tweets->flatten()->toArray());

        $tweets = Tweet::all();

        // create 2 replies for each fake tweet
        $replies = $tweets->map(
            function (Tweet $tweet) {
                return factory(Tweet::class, 4)->make([
                    'parent_id' => $tweet->id,
                ]);
            }
        );

        DB::table('tweets')->insert($replies->flatten()->toArray());
    }
}
