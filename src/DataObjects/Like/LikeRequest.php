<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Like;

final class LikeRequest
{
    public int $userId;
    public int $tweetId;
}
