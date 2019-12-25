<?php

use Apathy\Discuss\Models\Image;
use Apathy\Discuss\Models\Poll;
use Apathy\Discuss\Models\PollOption;
use Apathy\Discuss\Models\Tweet;
use Illuminate\Database\Seeder;

class AttachmentsTablesSeeder extends Seeder
{
    public function run()
    {
        $tweetsCount = Tweet::count();
        $tweetsWithImagesCount = $tweetsCount / 5;
        $tweetsWithPollCount = $tweetsCount / 10;

        factory(Poll::class, $tweetsWithPollCount)
            ->create()
            ->each(function (Poll $poll) {
                $poll->options()->saveMany(
                    factory(PollOption::class, 3)->make()
                );
            });

        $polls = Poll::all();
        $tweetsWithPolls = Tweet::all()->random($tweetsWithPollCount);
        $tweetsWithPolls->map(function (Tweet $tweet) use ($polls) {
            $tweet->poll()->save($polls->pop());
        });

        $tweetsWithImages = Tweet::whereNotIn('id', $tweetsWithPolls->pluck('id'))
            ->get()
            ->random($tweetsWithImagesCount);
        $tweetsWithImages->map(function (Tweet $tweet) {
            $tweet->images()->saveMany(
                factory(Image::class, random_int(1, 3))->make()
            );
        });
    }
}
