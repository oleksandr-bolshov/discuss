<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\User;

final class UpdateUserRequest
{
    public int $id;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $email;
    public ?string $username;
    public ?string $password;
    public ?string $profileImage;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->id = $data['id'];
        $request->firstName = $data['first_name'] ?? null;
        $request->lastName = $data['last_name'] ?? null;
        $request->email = $data['email'] ?? null;
        $request->username = $data['username'] ?? null;
        $request->password = $data['password'] ?? null;
        $request->profileImage = $data['profile_image'] ?? null;

        return $request;
    }
}
