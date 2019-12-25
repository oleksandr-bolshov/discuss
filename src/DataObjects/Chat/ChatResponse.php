<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Chat;

use Apathy\Discuss\DataObjects\Message\MessageResponse;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class ChatResponse
{
    public int $id;
    public MessageResponse $lastMessage;
    public Collection $members;
    public Carbon $createdAt;
}
