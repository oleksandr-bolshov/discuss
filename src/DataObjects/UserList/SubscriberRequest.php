<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

final class SubscriberRequest
{
    public int $listId;
    public int $subscriberId;
}
