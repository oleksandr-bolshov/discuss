<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\PaginateByIdRequest;
use Apathy\Discuss\DataObjects\Tweet\CreateTweetRequest;
use Apathy\Discuss\DataObjects\Tweet\PaginateRequest;
use Apathy\Discuss\DataObjects\Tweet\TweetResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface TweetService
{
    public function find(int $id): TweetResponse;

    public function paginate(PaginateRequest $paginationRequest): Paginator;

    public function paginateByUserId(PaginateByIdRequest $paginationRequest): Paginator;

    public function paginateByListId(PaginateByIdRequest $paginationRequest): Paginator;

    public function create(CreateTweetRequest $request): void;

    public function delete(int $id): void;
}
