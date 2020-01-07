<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

final class SubscriberRequest
{
    public int $listId;
    public int $subscriberId;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->listId = $data['list_id'];
        $request->subscriberId = $data['subscriber_id'];

        return $request;
    }
}
