<?php

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\TweetService;
use Apathy\Discuss\DataObjects\Image\CreateImageRequest;
use Apathy\Discuss\DataObjects\Poll\CreatePollOptionRequest;
use Apathy\Discuss\DataObjects\Poll\CreatePollRequest;
use Apathy\Discuss\DataObjects\Tweet\CreateTweetRequest;
use Apathy\Discuss\DataObjects\Tweet\TweetResponse;
use Apathy\Discuss\Enum\ListUserType;
use Apathy\Discuss\Models\Image;
use Apathy\Discuss\Models\Poll;
use Apathy\Discuss\Models\PollOption;
use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Models\UserList;
use Apathy\Discuss\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class TweetServiceTest extends TestCase
{
    use RefreshDatabase;

    private TweetService $tweetService;
    private int $userId;

    public function setUp(): void
    {
        parent::setUp();
        $this->tweetService = $this->app->make(TweetService::class);
        $this->userId = factory(User::class)->create()->id;
    }

    public function test_find()
    {
        $parentTweetId = factory(Tweet::class)->create()->id;
        $expected = [
            'text' => 'Test tweet',
            'parent_id' => $parentTweetId,
        ];
        $tweet = factory(Tweet::class)->create([
            'text' => $expected['text'],
            'parent_id' => $expected['parent_id'],
        ]);
        $tweet->images()->saveMany(factory(Image::class, 2)->make());

        $poll = factory(Poll::class)->create();
        $poll->options()->saveMany(factory(PollOption::class, 2)->make());
        $tweet->poll()->save($poll);

        $tweet = $this->tweetService->find($tweet->id);

        $this->assertEquals($expected['text'], $tweet->text);
        $this->assertEquals($expected['parent_id'], $tweet->inReplyToTweet->id);
        $this->assertEquals($this->userId, $tweet->author->id);
        $this->assertEmpty($tweet->replies);
        $this->assertEquals(0, $tweet->repliesCount);
        $this->assertEquals(1, $tweet->inReplyToTweet->repliesCount);
        $this->assertEquals(0, $tweet->likesCount);
        $this->assertEquals($poll->id, $tweet->poll->id);
        $this->assertEquals($poll->options->pluck('option'), $tweet->poll->options->pluck('option'));
        $this->assertNotEmpty($tweet->images);
    }

    public function test_paginate()
    {
        $page = 1;
        $perPage = 20;

        factory(Tweet::class, 20)->create();
        $tweets = $this->tweetService->paginate($page, $perPage);
        $this->assertCount($perPage, $tweets);
        foreach ($tweets as $tweet) {
            $this->assertInstanceOf(TweetResponse::class, $tweet);
        }
    }

    public function test_paginate_by_user_id()
    {
        $page = 1;
        $perPage = 10;

        factory(Tweet::class, 12)->create();
        $tweets = $this->tweetService->paginateByUserId($this->userId, $page, $perPage);
        $this->assertCount($perPage, $tweets);
        foreach ($tweets as $tweet) {
            $this->assertInstanceOf(TweetResponse::class, $tweet);
        }
    }

    public function test_paginate_by_list_id()
    {
        $expectedTweets = factory(Tweet::class, 10)
            ->create()
            ->toBase()
            ->sortBy('id')
            ->values();
        $listId = factory(UserList::class)->create()->id;
        DB::table('list_user')->insert([
            'list_id' => $listId,
            'user_id' => $this->userId,
            'user_type' => ListUserType::MEMBER,
        ]);

        factory(Tweet::class, 10)->create([
            'author_id' => factory(User::class)->create()->id,
        ]);

        $actualTweets = $this->tweetService
            ->paginateByListId($listId)
            ->toBase()
            ->sortBy('id')
            ->values();

        foreach (range(0, $expectedTweets->count() - 1) as $i) {
            $this->assertEquals($expectedTweets[$i]->id, $actualTweets[$i]->id);
            $this->assertEquals($expectedTweets[$i]->text, $actualTweets[$i]->text);
            $this->assertEquals($expectedTweets[$i]->author_id, $actualTweets[$i]->author->id);
        }
    }

    public function test_create()
    {
        $expectedTweet = [
            'text' => 'test text',
            'author_id' => $this->userId,
        ];

        $expectedPoll = [
            'title' => 'poll title',
            'end_datetime' => Carbon::create(2020),
        ];

        $tweet = new CreateTweetRequest();
        $tweet->text = $expectedTweet['text'];
        $tweet->authorId = $expectedTweet['author_id'];
        $tweet->inReplyToTweetId = factory(Tweet::class)->create()->id;

        $poll = new CreatePollRequest();
        $poll->title = $expectedPoll['title'];
        $poll->endDatetime = $expectedPoll['end_datetime'];

        $pollOptions = collect();
        foreach (range(0, 2) as $i) {
            $pollOption = new CreatePollOptionRequest();
            $pollOption->option = "fake option {$i}";

            $pollOptions->push($pollOption);
        }
        $poll->options = $pollOptions;
        $tweet->poll = $poll;

        $images = collect();
        foreach (range(0, 3) as $i) {
            $image = new CreateImageRequest();
            $image->path = "fake path {$i}";

            $images->push($image);
        }
        $tweet->images = $images;

        $this->tweetService->create($tweet);

        $this->assertDatabaseHas('tweets', $expectedTweet);
        $this->assertDatabaseHas('polls', $expectedPoll);

        foreach (range(0, $pollOptions->count() - 1) as $i) {
            $this->assertDatabaseHas('poll_options', [
                'option' => "fake option {$i}",
            ]);
        }

        foreach (range(0, $images->count() - 1) as $i) {
            $this->assertDatabaseHas('images', [
                'path' => "fake path {$i}",
            ]);
        }
    }

    public function test_delete()
    {
        $expectedDeletedText = ['text' => 'tweet text'];
        $tweetId = factory(Tweet::class)->create($expectedDeletedText)->id;
        $this->tweetService->delete($tweetId);
        $this->assertDeleted('tweets', $expectedDeletedText);
    }
}
