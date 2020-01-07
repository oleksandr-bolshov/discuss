<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\User;

final class CreateUserRequest
{
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $username;
    public string $password;
    public ?string $profileImage;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->firstName = $data['first_name'];
        $request->lastName = $data['last_name'];
        $request->email = $data['email'];
        $request->username = $data['username'];
        $request->password = $data['password'];
        $request->profileImage = $data['profile_image'] ?? null;

        return $request;
    }
}
