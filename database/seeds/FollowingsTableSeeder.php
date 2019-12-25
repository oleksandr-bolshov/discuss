<?php

use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowingsTableSeeder extends Seeder
{
    private const FOLLOWERS_COUNT = 10;

    public function run()
    {
        $users = User::all();
        $now = Carbon::now();

        $followers = $users->map(
            function (User $user) use ($now, $users) {
                $followersId = $users->whereNotIn('id', $user->id)
                    ->shuffle()
                    ->shuffle()
                    ->take(self::FOLLOWERS_COUNT)
                    ->pluck('id');

                return $followersId->map(
                    function (int $followerId) use ($now, $user) {
                        return [
                            'follower_id' => $followerId,
                            'user_id' => $user->id,
                            'created_at' => $now->toDateTimeString(),
                        ];
                    }
                );
            }
        );

        DB::table('followings')->insert($followers->flatten(1)->toArray());
    }
}
