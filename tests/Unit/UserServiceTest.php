<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\UserService;
use Apathy\Discuss\DataObjects\User\CreateUserRequest;
use Apathy\Discuss\DataObjects\User\UpdateUserRequest;
use Apathy\Discuss\DataObjects\User\UserResponse as UserResponse;
use Apathy\Discuss\Enum\ListUserType;
use Apathy\Discuss\Models\Like;
use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Models\UserList;
use Apathy\Discuss\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    private const USER_DATA = [
        'first_name' => 'test_first_name',
        'last_name' => 'test_last_name',
        'email' => 'test_email@example.com',
        'username' => 'test_username',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make(UserService::class);
    }

    public function test_find()
    {
        factory(User::class)->create(self::USER_DATA);
        $actual = $this->userService->find(1);

        $this->assertEquals($actual->firstName, self::USER_DATA['first_name']);
        $this->assertEquals($actual->lastName, self::USER_DATA['last_name']);
        $this->assertEquals($actual->email, self::USER_DATA['email']);
        $this->assertEquals($actual->username, self::USER_DATA['username']);
    }

    public function test_paginate_users_who_liked_by_tweet_id()
    {
        $users = factory(User::class, 20)->create();
        $tweet = factory(Tweet::class)->create();
        $users->map(function (User $user) use ($tweet) {
            factory(Like::class)->create([
                'user_id' => $user->id,
            ]);
        });

        $users = $this->userService->paginateUsersWhoLikedByTweetId($tweet->id);

        $this->assertCount(15, $users);
        foreach ($users as $user) {
            $this->assertInstanceOf(UserResponse::class, $user);
        }
    }

    public function test_paginate_followers_by_user_id()
    {
        $userId = factory(User::class)->create(self::USER_DATA)->id;
        $followers = factory(User::class, 20)->create();
        $followers->map(fn (User $follower) => $follower->followings()->attach($userId));

        $userFollowers = $this->userService->paginateFollowersByUserId($userId);
        $this->assertCount(15, $userFollowers);
        foreach ($userFollowers as $userFollower) {
            $this->assertInstanceOf(UserResponse::class, $userFollower);
        }
    }

    public function test_paginate_followings_by_user_id()
    {
        $userId = factory(User::class)->create(self::USER_DATA)->id;
        $followings = factory(User::class, 20)->create();

        $followings->map(fn (User $following) => $following->followers()->attach($userId));
        $userFollowings = $this->userService->paginateFollowingsByUserId($userId);
        $this->assertCount(15, $userFollowings);
        foreach ($userFollowings as $userFollowing) {
            $this->assertInstanceOf(UserResponse::class, $userFollowing);
        }
    }

    public function test_paginate_subscribers_by_list_id()
    {
        factory(User::class)->create();
        $listId = factory(UserList::class)->create()->id;
        factory(User::class, 20)->create();

        $expectedSubscribers = User::inRandomOrder()
            ->take(10)
            ->get()
            ->sortBy('id')
            ->values();
        $listSubscribers = $expectedSubscribers->map(fn (User $user) => [
            'list_id' => $listId,
            'user_id' => $user->id,
            'user_type' => ListUserType::SUBSCRIBER,
        ])->toArray();

        DB::table('list_user')->insert($listSubscribers);

        $actualSubscribers = $this->userService
            ->paginateSubscribersByListId($listId)
            ->toBase()
            ->sortBy('id')
            ->values();

        foreach (range(0, $expectedSubscribers->count() - 1) as $i) {
            $this->assertEquals($expectedSubscribers[$i]->id, $actualSubscribers[$i]->id);
            $this->assertEquals($expectedSubscribers[$i]->username, $actualSubscribers[$i]->username);
        }
    }

    public function test_paginate_members_by_list_id()
    {
        factory(User::class)->create();
        $listId = factory(UserList::class)->create()->id;
        factory(User::class, 20)->create();

        $expectedMembers = User::inRandomOrder()
            ->take(10)
            ->get()
            ->sortBy('id')
            ->values();
        $listSubscribers = $expectedMembers->map(fn (User $user) => [
            'list_id' => $listId,
            'user_id' => $user->id,
            'user_type' => ListUserType::MEMBER,
        ])->toArray();

        DB::table('list_user')->insert($listSubscribers);

        $actualMembers = $this->userService
            ->paginateMembersByListId($listId)
            ->toBase()
            ->sortBy('id')
            ->values();

        foreach (range(0, $expectedMembers->count() - 1) as $i) {
            $this->assertEquals($expectedMembers[$i]->id, $actualMembers[$i]->id);
            $this->assertEquals($expectedMembers[$i]->username, $actualMembers[$i]->username);
        }
    }

    public function test_create()
    {
        $createUserRequest = new CreateUserRequest();

        $createUserRequest->firstName = self::USER_DATA['first_name'];
        $createUserRequest->lastName = self::USER_DATA['last_name'];
        $createUserRequest->email = self::USER_DATA['email'];
        $createUserRequest->username = self::USER_DATA['username'];
        $createUserRequest->password = 'password';

        $this->userService->create($createUserRequest);

        $this->assertDatabaseHas('users', self::USER_DATA);
    }

    public function test_create_with_invalid_input()
    {
        $createUserRequest = new CreateUserRequest();
        $this->expectException(InvalidArgumentException::class);
        $this->userService->create($createUserRequest);
    }

    public function test_create_not_unique_email()
    {
        factory(User::class)->create(self::USER_DATA);
        $createUserRequest = new CreateUserRequest();

        $createUserRequest->firstName = self::USER_DATA['first_name'];
        $createUserRequest->lastName = self::USER_DATA['last_name'];
        $createUserRequest->email = self::USER_DATA['email'];
        $createUserRequest->username = self::USER_DATA['username'];
        $createUserRequest->password = 'password';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The email has already been taken');
        $this->userService->create($createUserRequest);
    }

    public function test_update()
    {
        $userId = factory(User::class)->create()->id;
        $expectedFirstName = 'updated_first_name';

        $updateUserRequest = new UpdateUserRequest();
        $updateUserRequest->id = $userId;
        $updateUserRequest->firstName = $expectedFirstName;

        $this->userService->update($updateUserRequest);

        $this->assertDatabaseHas('users', [
            'first_name' => $expectedFirstName,
        ]);
    }

    public function test_delete()
    {
        $userId = factory(User::class)->create(self::USER_DATA)->id;
        $this->userService->delete($userId);
        $this->assertDeleted('users', self::USER_DATA);
    }
}
