<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\ChatService;
use Apathy\Discuss\DataObjects\Chat\CreateChatRequest;
use Apathy\Discuss\DataObjects\Message\MessageResponse;
use Apathy\Discuss\DataObjects\PaginateByIdRequest;
use Apathy\Discuss\Models\Chat as ChatModel;
use Apathy\Discuss\Models\Message as MessageModel;
use Apathy\Discuss\Models\User as UserModel;
use Apathy\Discuss\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ChatServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChatService $chatService;

    public function setUp(): void
    {
        parent::setUp();
        $this->chatService = $this->app->make(ChatService::class);
    }

    public function test_paginate_chats_by_user_id()
    {
        $userId = factory(UserModel::class)->create()->id;
        factory(ChatModel::class, 20)
            ->create()
            ->each(function (ChatModel $chat) use ($userId) {
                $chat->members()->attach($userId);
                $chat->messages()->saveMany([factory(MessageModel::class)->make()]);
            });

        $chats = $this->chatService->paginateChatsByUserId(
            PaginateByIdRequest::fromArray([
                'id' => $userId,
            ])
        )->toBase();

        $lastChatsMessages = $chats->pluck('lastMessage');

        $lastChatsMessages->reduce(function (MessageResponse $prevMessage, MessageResponse $message) {
            $this->assertTrue($prevMessage->createdAt->greaterThanOrEqualTo($message->createdAt));

            return $message;
        }, $lastChatsMessages[0]);
    }

    public function test_create()
    {
        $users = factory(UserModel::class, 2)->create();
        $users = $users->map->toResponse()->pluck('id')->toArray();

        $this->chatService->create(CreateChatRequest::fromArray([
            'members_ids' => $users
        ]));

        foreach ($users as $user) {
            $this->assertDatabaseHas('chat_user', [
                'user_id' => $user,
            ]);
        }
    }

    public function test_create_not_unique_chat()
    {
        $users = factory(UserModel::class, 2)->create();
        $users = $users->map->toResponse()->pluck('id')->toArray();

        $chatId = factory(ChatModel::class)->create()->id;

        DB::table('chat_user')->insert([
            [
                'user_id' => $users[0],
                'chat_id' => $chatId,
                'created_at' => Carbon::now(),
            ], [
                'user_id' => $users[1],
                'chat_id' => $chatId,
                'created_at' => Carbon::now(),
            ],
        ]);

        $expectedChatsCount = DB::table('chats')->count();

        $this->chatService->create(CreateChatRequest::fromArray([
            'members_ids' => $users
        ]));

        $actualChatsCount = DB::table('chats')->count();

        $this->assertEquals($expectedChatsCount, $actualChatsCount);
    }

    public function test_chat_exists()
    {
        $users = factory(UserModel::class, 2)->create();
        $users = $users->map->toResponse()->pluck('id')->toArray();

        $chatId = factory(ChatModel::class)->create()->id;

        DB::table('chat_user')->insert([
            [
                'user_id' => $users[0],
                'chat_id' => $chatId,
                'created_at' => Carbon::now(),
            ], [
                'user_id' => $users[1],
                'chat_id' => $chatId,
                'created_at' => Carbon::now(),
            ],
        ]);

        $this->assertTrue($this->chatService->chatExists(
            CreateChatRequest::fromArray([
                'members_ids' => $users,
            ])
        ));
    }

    public function test_chat_exists_when_false()
    {
        $this->assertFalse($this->chatService->chatExists(
            CreateChatRequest::fromArray([
                'members_ids' => [998, 999],
            ])
        ));
    }

    public function test_delete()
    {
        $chatId = factory(ChatModel::class)->create()->id;
        $this->chatService->delete($chatId);
        $this->assertEquals(0, DB::table('chats')->count());
    }
}
