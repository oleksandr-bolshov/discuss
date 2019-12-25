<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Poll;

final class RetractRequest
{
    public int $userId;
    public int $pollOptionId;
}
