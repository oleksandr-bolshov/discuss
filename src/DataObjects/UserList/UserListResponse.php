<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

use Apathy\Discuss\DataObjects\User\UserResponse;
use Carbon\Carbon;

final class UserListResponse
{
    public int $id;
    public string $title;
    public ?string $description;
    public UserResponse $owner;
    public int $membersCount;
    public int $subscribersCount;
    public Carbon $createdAt;
    public Carbon $updatedAt;
}
