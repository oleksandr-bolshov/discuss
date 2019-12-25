<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\User;

final class UpdateUserRequest
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $username;
    public string $password;
    public ?string $profileImage;
}
