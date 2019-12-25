<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\User\CreateUserRequest;
use Apathy\Discuss\DataObjects\User\UpdateUserRequest;
use Apathy\Discuss\DataObjects\User\UserResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface UserService extends Pagination
{
    public function find(int $id): UserResponse;

    public function paginateUsersWhoLikedByTweetId(
        int $tweetId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function paginateFollowersByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function paginateFollowingsByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function paginateSubscribersByListId(
        int $listId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function paginateMembersByListId(
        int $listId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function create(CreateUserRequest $request): void;

    public function update(UpdateUserRequest $request): void;

    public function delete(int $id): void;
}
