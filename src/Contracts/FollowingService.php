<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Following\FollowingRequest;

interface FollowingService
{
    public function follow(FollowingRequest $request): void;

    public function isFollows(FollowingRequest $request): bool;

    public function unfollow(FollowingRequest $request): void;
}
