<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

use Illuminate\Support\Collection;

final class CreateUserListRequest
{
    public string $title;
    public ?string $description;
    public int $ownerId;
    public Collection $membersIds;
}
