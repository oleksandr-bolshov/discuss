<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Message;

final class CreateMessageRequest
{
    public int $chatId;
    public int $userId;
    public string $text;
    public bool $isRead;
}
