<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\User;

use Carbon\Carbon;

final class UserResponse
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $username;
    public string $password;
    public ?string $profileImage;
    public int $followersCount;
    public Carbon $createdAt;
    public Carbon $updatedAt;
}
