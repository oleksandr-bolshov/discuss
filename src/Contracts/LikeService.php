<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Like\LikeRequest;

interface LikeService
{
    public function like(LikeRequest $request): void;

    public function isLikes(LikeRequest $request): bool;

    public function unlike(LikeRequest $request): void;
}
