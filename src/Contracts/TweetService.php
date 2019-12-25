<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Tweet\CreateTweetRequest;
use Apathy\Discuss\DataObjects\Tweet\TweetResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface TweetService extends Pagination
{
    public function find(int $id): TweetResponse;

    public function paginate(
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function paginateByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function paginateByListId(
        int $listId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function create(CreateTweetRequest $request): void;

    public function delete(int $id): void;
}
