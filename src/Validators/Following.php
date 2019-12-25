<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\Following\FollowingRequest;

final class Following extends Validator
{
    public function validateFollowingRequest(FollowingRequest $request): void
    {
        $this->validate($request, [
            'followerId' => 'required|exists:users,id',
            'userId' => 'required|exists:users,id',
        ]);
    }
}
