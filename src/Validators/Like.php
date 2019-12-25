<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\Like\LikeRequest;

final class Like extends Validator
{
    public function validateLikeRequest(LikeRequest $request): void
    {
        $this->validate($request, [
            'userId' => 'required|exists:users,id',
            'tweetId' => 'required|exists:tweets,id',
        ]);
    }
}
