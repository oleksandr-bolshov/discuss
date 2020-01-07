<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\LikeService;
use Apathy\Discuss\DataObjects\Like\LikeRequest;
use Apathy\Discuss\Models\Like;
use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class LikeServiceTest extends TestCase
{
    use RefreshDatabase;

    private LikeService $likeService;
    private LikeRequest $like;

    public function setUp(): void
    {
        parent::setUp();
        $this->likeService = $this->app->make(LikeService::class);
        $userId = factory(User::class)->create()->id;
        $tweetId = factory(Tweet::class)->create()->id;

        $this->like = LikeRequest::fromArray([
            'tweet_id' => $tweetId,
            'user_id' => $userId,
        ]);
    }

    public function test_like()
    {
        $expected = [
            'user_id' => $this->like->userId,
            'tweet_id' => $this->like->tweetId,
        ];

        $this->likeService->like($this->like);

        $this->assertDatabaseHas('likes', $expected);
    }

    public function test_like_when_not_unique()
    {
        $expected = [
            'user_id' => $this->like->userId,
            'tweet_id' => $this->like->tweetId,
        ];

        factory(Like::class)->create($expected);

        $expectedLikesCount = DB::table('likes')->count();

        $this->likeService->like($this->like);

        $actualLikesCount = DB::table('likes')->count();

        $this->assertEquals($expectedLikesCount, $actualLikesCount);
    }

    public function test_unlike()
    {
        $expected = [
            'user_id' => $this->like->userId,
            'tweet_id' => $this->like->tweetId,
        ];

        factory(Like::class)->create($expected);

        $this->likeService->unlike($this->like);

        $this->assertDeleted('likes', $expected);
    }

    public function test_is_likes_when_true()
    {
        factory(Like::class)->create();

        $this->assertTrue($this->likeService->isLikes($this->like));
    }

    public function test_is_likes_when_false()
    {
        $anotherUserId = factory(User::class)->create()->id;

        $this->assertFalse($this->likeService->isLikes(
            LikeRequest::fromArray([
                'user_id' => $anotherUserId,
                'tweet_id' => $this->like->tweetId,
            ])
        ));
    }
}
