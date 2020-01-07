<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\FollowingService;
use Apathy\Discuss\DataObjects\Following\FollowingRequest;
use Apathy\Discuss\Models\Following;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class FollowingServiceTest extends TestCase
{
    use RefreshDatabase;

    private FollowingService $followingService;
    private FollowingRequest $following;

    public function setUp(): void
    {
        parent::setUp();
        $this->followingService = $this->app->make(FollowingService::class);
        [$followerId, $userId] = factory(User::class, 2)->create()->pluck('id');

        $this->following = FollowingRequest::fromArray([
            'follower_id' => $followerId,
            'user_id' => $userId,
        ]);
    }

    public function test_follows()
    {
        $expected = [
            'user_id' => $this->following->userId,
            'follower_id' => $this->following->followerId,
        ];

        $this->followingService->follow($this->following);

        $this->assertDatabaseHas('followings', $expected);
    }

    public function test_follows_when_not_unique()
    {
        $expected = [
            'user_id' => $this->following->userId,
            'follower_id' => $this->following->followerId,
        ];

        factory(Following::class)->create($expected);

        $expectedFollowingsCount = DB::table('followings')->count();

        $this->followingService->follow($this->following);

        $actualFollowingsCount = DB::table('followings')->count();

        $this->assertEquals($expectedFollowingsCount, $actualFollowingsCount);
    }

    public function test_unfollow()
    {
        $expected = [
            'user_id' => $this->following->userId,
            'follower_id' => $this->following->followerId,
        ];

        factory(Following::class)->create($expected);

        $this->followingService->unfollow($this->following);

        $this->assertDeleted('followings', $expected);
    }

    public function test_is_follows_when_true()
    {
        $expected = [
            'user_id' => $this->following->userId,
            'follower_id' => $this->following->followerId,
        ];
        factory(Following::class)->create($expected);

        $this->assertTrue($this->followingService->isFollows($this->following));
    }

    public function test_is_follows_when_false()
    {
        $anotherUserId = factory(User::class)->create()->id;
        $followingThatNotExists = new FollowingRequest();
        $followingThatNotExists->userId = $anotherUserId;
        $followingThatNotExists->followerId = $this->following->followerId;

        $this->assertFalse($this->followingService->isFollows(
            FollowingRequest::fromArray([
                'user_id' => $anotherUserId,
                'follower_id' => $this->following->followerId,
            ])
        ));
    }
}
