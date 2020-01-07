<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Poll;

final class VoteRequest
{
    public int $userId;
    public int $pollId;
    public int $pollOptionId;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->userId = $data['user_id'];
        $request->pollId = $data['poll_id'];
        $request->pollOptionId = $data['poll_option_id'];
        return $request;
    }
}
