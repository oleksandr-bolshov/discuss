<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Poll;

final class VoteRequest
{
    public int $userId;
    public int $pollId;
    public int $pollOptionId;
}
