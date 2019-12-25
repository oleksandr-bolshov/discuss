<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\User\CreateUserRequest;
use Apathy\Discuss\DataObjects\User\UpdateUserRequest;
use Illuminate\Validation\Rule;

final class User extends Validator
{
    public function validateCreateRequest(CreateUserRequest $request): void
    {
        $this->validate($request, [
            'firstName' => 'required|string|between:2,50',
            'lastName' => 'required|string|between:2,50',
            'email' => 'required|email|unique:users|max:255',
            'username' => 'required|alpha_dash|between:3,50|unique:users',
            'password' => 'required|string|min:8',
            'profileImage' => 'nullable',
        ]);
    }

    public function validateUpdateRequest(UpdateUserRequest $request): void
    {
        $this->validate($request, [
            'id' => 'required|exists:users',
            'firstName' => 'nullable|string|between:2,50',
            'lastName' => 'nullable|string|between:2,50',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->id),
            ],
            'username' => [
                'nullable',
                'alpha_dash',
                'between:3,50',
                Rule::unique('users')->ignore($request->id),
            ],
            'password' => 'nullable|string|between:8,50',
            'profileImage' => 'nullable',
        ]);
    }
}
