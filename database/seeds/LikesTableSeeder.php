<?php

use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LikesTableSeeder extends Seeder
{
    private const LIKES_COUNT = 10;

    public function run()
    {
        $tweets = Tweet::all();
        $users = User::all();
        $now = Carbon::now();

        $tweetLikes = $tweets->map(
            function (Tweet $tweet) use ($users, $now) {
                $userIds = $users->shuffle()->shuffle()->take(self::LIKES_COUNT)->pluck('id');

                return $userIds->map(
                    function (int $userId) use ($tweet, $now) {
                        return [
                            'user_id' => $userId,
                            'tweet_id' => $tweet->id,
                            'created_at' => $now->toDateTimeString(),
                        ];
                    }
                );
            }
        );

        DB::table('likes')->insert($tweetLikes->flatten(1)->toArray());
    }
}
