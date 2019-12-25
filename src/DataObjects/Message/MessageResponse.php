<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Message;

use Apathy\Discuss\DataObjects\User\UserResponse;
use Carbon\Carbon;

final class MessageResponse
{
    public int $id;
    public int $chatId;
    public UserResponse $user;
    public string $text;
    public bool $isRead;
    public Carbon $createdAt;
}
