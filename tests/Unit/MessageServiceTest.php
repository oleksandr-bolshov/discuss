<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\MessageService;
use Apathy\Discuss\DataObjects\Message\CreateMessageRequest;
use Apathy\Discuss\DataObjects\Message\MessageResponse;
use Apathy\Discuss\DataObjects\PaginateByIdRequest;
use Apathy\Discuss\Models\Chat as ChatModel;
use Apathy\Discuss\Models\Message as MessageModel;
use Apathy\Discuss\Models\User as UserModel;
use Apathy\Discuss\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    private MessageService $messageService;
    private int $userId;
    private int $chatId;

    public function setUp(): void
    {
        parent::setUp();
        $this->messageService = $this->app->make(MessageService::class);
        $this->userId = factory(UserModel::class)->create()->id;
        $this->chatId = factory(ChatModel::class)->create()->id;
        DB::table('chat_user')->insert([
            'chat_id' => $this->chatId,
            'user_id' => $this->userId,
            'created_at' => Carbon::now(),
        ]);
    }

    public function test_paginate_messages_by_chat_id()
    {
        factory(MessageModel::class, 20)->create();

        $messages = $this->messageService->paginateMessagesByChatId(
            PaginateByIdRequest::fromArray([
                'id' => $this->chatId,
            ])
        );

        $this->assertCount(15, $messages);
        foreach ($messages as $message) {
            $this->assertInstanceOf(MessageResponse::class, $message);
        }
    }

    public function test_last_message_by_chat_id()
    {
        $latestCreatedAt = Carbon::now();

        $messages = factory(MessageModel::class, 20)->create();
        factory(MessageModel::class)->create([
            'created_at' => $latestCreatedAt,
        ]);

        $latestMessage = $this->messageService->lastMessageByChatId($this->chatId);

        $this->assertEquals($latestCreatedAt->toDateTimeString(), $latestMessage->createdAt->toDateTimeString());
        foreach ($messages as $message) {
            $this->assertTrue($latestMessage->createdAt->greaterThanOrEqualTo($message->created_at));
        }
    }

    public function test_create()
    {
        $expected = [
            'chat_id' => $this->chatId,
            'user_id' => $this->userId,
            'text' => 'test message',
            'is_read' => false,
        ];

        $this->messageService->create(CreateMessageRequest::fromArray($expected));

        $this->assertDatabaseHas('messages', $expected);
    }

    public function test_read()
    {
        $expected = [
            'chat_id' => $this->chatId,
            'user_id' => $this->userId,
            'text' => 'test message',
            'is_read' => false,
        ];

        $message = factory(MessageModel::class)->create($expected);
        $expected['is_read'] = true;

        $this->messageService->read($message->id);

        $this->assertDatabaseHas('messages', $expected);
    }

    public function test_delete()
    {
        $expected = [
            'chat_id' => $this->chatId,
            'user_id' => $this->userId,
            'text' => 'test message',
            'is_read' => false,
        ];

        $message = factory(MessageModel::class)->create($expected);

        $this->messageService->delete($message->id);

        $this->assertDeleted('messages', $expected);
    }
}
