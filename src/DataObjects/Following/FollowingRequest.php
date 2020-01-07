<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Following;

final class FollowingRequest
{
    public int $followerId;
    public int $userId;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->followerId = $data['follower_id'];
        $request->userId = $data['user_id'];
        return $request;
    }
}
