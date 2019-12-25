<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Following;

final class FollowingRequest
{
    public int $followerId;
    public int $userId;
}
