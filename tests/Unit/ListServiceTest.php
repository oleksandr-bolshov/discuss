<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\ListService;
use Apathy\Discuss\DataObjects\PaginationRequest;
use Apathy\Discuss\DataObjects\UserList\CreateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\MemberRequest;
use Apathy\Discuss\DataObjects\UserList\SubscriberRequest;
use Apathy\Discuss\DataObjects\UserList\UpdateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\UserListResponse;
use Apathy\Discuss\Enum\ListUserType;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Models\UserList;
use Apathy\Discuss\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ListServiceTest extends TestCase
{
    use RefreshDatabase;

    private ListService $listService;
    private int $ownerId;

    public function setUp(): void
    {
        parent::setUp();
        $this->listService = $this->app->make(ListService::class);
        $this->ownerId = factory(User::class)->create()->id;
    }

    public function test_find()
    {
        $expected = [
            'title' => 'test title',
            'owner_id' => $this->ownerId,
        ];

        $listId = factory(UserList::class)->create([
            'title' => $expected['title'],
        ])->id;

        $list = $this->listService->find($listId);

        $this->assertEquals($expected['title'], $list->title);
        $this->assertEquals($expected['owner_id'], $list->owner->id);
    }

    public function test_paginate_by_owner_id()
    {
        factory(UserList::class, 20)->create();

        $paginationRequest = new PaginationRequest();
        $paginationRequest->id = $this->ownerId;

        $lists = $this->listService->paginateByOwnerId($paginationRequest);

        $this->assertCount(15, $lists);
        foreach ($lists as $list) {
            $this->assertInstanceOf(UserListResponse::class, $list);
        }
    }

    public function test_paginate_by_subscriber_id()
    {
        factory(UserList::class, 20)->create();
        $subscriberId = factory(User::class)->create()->id;

        $expectedLists = UserList::inRandomOrder()
            ->take(10)
            ->get()
            ->sortBy('id')
            ->values();
        $listsSubscriber = $expectedLists->map(fn (UserList $list) => [
            'list_id' => $list->id,
            'user_id' => $subscriberId,
            'user_type' => ListUserType::SUBSCRIBER,
        ])->toArray();

        DB::table('list_user')->insert($listsSubscriber);

        $paginationRequest = new PaginationRequest();
        $paginationRequest->id = $subscriberId;

        $actualLists = $this->listService->paginateBySubscriberId($paginationRequest);

        $this->assertEquals(
            $expectedLists->pluck('id', 'title'),
            $actualLists->pluck('id', 'title')
        );
    }

    public function test_paginate_by_member_id()
    {
        factory(UserList::class, 20)->create();
        $memberId = factory(User::class)->create()->id;

        $expectedLists = UserList::inRandomOrder()
            ->take(10)
            ->get()
            ->sortBy('id')
            ->values();
        $listsMember = $expectedLists->map(fn (UserList $list) => [
            'list_id' => $list->id,
            'user_id' => $memberId,
            'user_type' => ListUserType::MEMBER,
        ])->toArray();

        DB::table('list_user')->insert($listsMember);

        $paginationRequest = new PaginationRequest();
        $paginationRequest->id = $memberId;

        $actualLists = $this->listService->paginateByMemberId($paginationRequest);

        $this->assertEquals(
            $expectedLists->pluck('id', 'title'),
            $actualLists->pluck('id', 'title')
        );
    }

    public function test_create_only_with_required_fields()
    {
        $expected = [
            'title' => 'test title',
            'owner_id' => $this->ownerId,
        ];

        $list = new CreateUserListRequest();
        $list->title = $expected['title'];
        $list->ownerId = $expected['owner_id'];

        $this->listService->create($list);

        $this->assertDatabaseHas('lists', $expected);
    }

    public function test_create_with_nullable_fields()
    {
        $expected = [
            'title' => 'test title',
            'description' => 'test description',
            'owner_id' => $this->ownerId,
        ];

        $membersIds = factory(User::class, 3)->create([
            'first_name' => 'listMember',
        ])->pluck('id');

        $list = new CreateUserListRequest();
        $list->title = $expected['title'];
        $list->description = $expected['description'];
        $list->ownerId = $expected['owner_id'];
        $list->membersIds = $membersIds;

        $this->listService->create($list);

        $this->assertDatabaseHas('lists', $expected);
        foreach ($membersIds as $membersId) {
            $this->assertDatabaseHas('list_user', [
                'user_id' => $membersId,
            ]);
        }
    }

    public function test_add_subscriber()
    {
        $listId = factory(UserList::class)->create()->id;
        $userId = factory(User::class)->create()->id;

        $subscriberRequest = new SubscriberRequest();
        $subscriberRequest->listId = $listId;
        $subscriberRequest->subscriberId = $userId;

        $this->listService->addSubscriber($subscriberRequest);

        $this->assertDatabaseHas('list_user', [
            'user_id' => $userId,
            'user_type' => ListUserType::SUBSCRIBER,
        ]);
    }

    public function test_add_subscriber_when_not_unique()
    {
        $listId = factory(UserList::class)->create()->id;
        $userId = factory(User::class)->create()->id;

        DB::table('list_user')->insert([
            'list_id' => $listId,
            'user_id' => $userId,
            'user_type' => ListUserType::SUBSCRIBER,
        ]);

        $subscriberRequest = new SubscriberRequest();
        $subscriberRequest->listId = $listId;
        $subscriberRequest->subscriberId = $userId;

        $expectedSubscribersCount = DB::table('list_user')->count();

        $this->listService->addSubscriber($subscriberRequest);

        $actualSubscribersCount = DB::table('list_user')->count();

        $this->assertEquals($expectedSubscribersCount, $actualSubscribersCount);
    }

    public function test_add_member()
    {
        $listId = factory(UserList::class)->create()->id;
        $userId = factory(User::class)->create()->id;

        $memberRequest = new MemberRequest();
        $memberRequest->listId = $listId;
        $memberRequest->memberId = $userId;

        $this->listService->addMember($memberRequest);

        $this->assertDatabaseHas('list_user', [
            'user_id' => $userId,
            'user_type' => ListUserType::MEMBER,
        ]);
    }

    public function test_add_members_when_not_unique()
    {
        $listId = factory(UserList::class)->create()->id;
        $userId = factory(User::class)->create()->id;

        DB::table('list_user')->insert([
            'list_id' => $listId,
            'user_id' => $userId,
            'user_type' => ListUserType::MEMBER,
        ]);

        $memberRequest = new MemberRequest();
        $memberRequest->listId = $listId;
        $memberRequest->memberId = $userId;

        $expectedMembersCount = DB::table('list_user')->count();

        $this->listService->addMember($memberRequest);

        $actualMembersCount = DB::table('list_user')->count();

        $this->assertEquals($expectedMembersCount, $actualMembersCount);
    }

    public function test_has_subscriber_when_true()
    {
        $listId = factory(UserList::class)->create()->id;
        $userId = factory(User::class)->create()->id;

        DB::table('list_user')->insert([
            'list_id' => $listId,
            'user_id' => $userId,
            'user_type' => ListUserType::SUBSCRIBER,
        ]);

        $subscriberRequest = new SubscriberRequest();
        $subscriberRequest->listId = $listId;
        $subscriberRequest->subscriberId = $userId;

        $this->assertTrue($this->listService->hasSubscriber($subscriberRequest));
    }

    public function test_has_subscriber_when_false()
    {
        $subscriberRequest = new SubscriberRequest();
        $subscriberRequest->listId = 999;
        $subscriberRequest->subscriberId = 999;

        $this->assertFalse($this->listService->hasSubscriber($subscriberRequest));
    }

    public function test_update()
    {
        $listId = factory(UserList::class)->create([
            'title' => 'initial title',
        ])->id;

        $expected = [
            'id' => $listId,
            'title' => 'updated title',
        ];

        $list = new UpdateUserListRequest();
        $list->id = $expected['id'];
        $list->title = $expected['title'];

        $this->listService->update($list);

        $this->assertDatabaseHas('lists', $expected);
    }

    public function test_delete()
    {
        $listId = factory(UserList::class)->create()->id;
        $this->listService->delete($listId);
        $this->assertEquals(0, DB::table('lists')->count());
    }

    public function test_remove_subscriber()
    {
        $listId = factory(UserList::class)->create()->id;
        $userId = factory(User::class)->create()->id;

        DB::table('list_user')->insert([
            'list_id' => $listId,
            'user_id' => $userId,
            'user_type' => ListUserType::SUBSCRIBER,
        ]);

        $subscriberRequest = new SubscriberRequest();
        $subscriberRequest->listId = $listId;
        $subscriberRequest->subscriberId = $userId;

        $this->listService->removeSubscriber($subscriberRequest);

        $this->assertDeleted('list_user', [
            'user_id' => $userId,
            'user_type' => ListUserType::SUBSCRIBER,
        ]);
    }

    public function test_remove_member()
    {
        $listId = factory(UserList::class)->create()->id;
        $userId = factory(User::class)->create()->id;

        DB::table('list_user')->insert([
            'list_id' => $listId,
            'user_id' => $userId,
            'user_type' => ListUserType::MEMBER,
        ]);

        $memberRequest = new MemberRequest();
        $memberRequest->listId = $listId;
        $memberRequest->memberId = $userId;

        $this->listService->removeMember($memberRequest);

        $this->assertDeleted('list_user', [
            'user_id' => $userId,
            'user_type' => ListUserType::MEMBER,
        ]);
    }
}
