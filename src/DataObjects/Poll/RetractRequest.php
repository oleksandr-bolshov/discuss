<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Poll;

final class RetractRequest
{
    public int $userId;
    public int $pollOptionId;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->userId = $data['user_id'];
        $request->pollOptionId = $data['poll_option_id'];
        return $request;
    }
}
