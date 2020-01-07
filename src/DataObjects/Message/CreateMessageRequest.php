<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Message;

final class CreateMessageRequest
{
    public int $chatId;
    public int $userId;
    public string $text;
    public bool $isRead;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->chatId = $data['chat_id'];
        $request->userId = $data['user_id'];
        $request->text = $data['text'];
        $request->isRead = $data['is_read'];

        return $request;
    }
}
