<?php

use Apathy\Discuss\Models\Poll;
use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VotesTablesSeeder extends Seeder
{
    public function run()
    {
        $polls = Poll::all();
        $users = User::all();
        $now = Carbon::now();

        $votes = $polls->map(
            function (Poll $poll) use ($users, $now) {
                return $users->map(
                    function (User $user) use ($poll, $now) {
                        return [
                            'user_id' => $user->id,
                            'poll_option_id' => $poll->options->random()->id,
                            'created_at' => $now->toDateTimeString(),
                        ];
                    }
                );
            }
        );

        DB::table('votes')->insert($votes->flatten(1)->toArray());
    }
}
