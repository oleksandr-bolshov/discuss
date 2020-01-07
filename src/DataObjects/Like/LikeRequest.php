<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Like;

final class LikeRequest
{
    public int $userId;
    public int $tweetId;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->userId = $data['user_id'];
        $request->tweetId = $data['tweet_id'];

        return $request;
    }
}
