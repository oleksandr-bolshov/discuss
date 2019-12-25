<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

final class UpdateUserListRequest
{
    public int $id;
    public string $title;
    public string $description;
}
