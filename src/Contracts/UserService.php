<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\PaginationRequest;
use Apathy\Discuss\DataObjects\User\CreateUserRequest;
use Apathy\Discuss\DataObjects\User\UpdateUserRequest;
use Apathy\Discuss\DataObjects\User\UserResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface UserService
{
    public function find(int $id): UserResponse;

    public function paginateUsersWhoLikedByTweetId(PaginationRequest $paginationRequest): Paginator;

    public function paginateFollowersByUserId(PaginationRequest $paginationRequest): Paginator;

    public function paginateFollowingsByUserId(PaginationRequest $paginationRequest): Paginator;

    public function paginateSubscribersByListId(PaginationRequest $paginationRequest): Paginator;

    public function paginateMembersByListId(PaginationRequest $paginationRequest): Paginator;

    public function create(CreateUserRequest $request): void;

    public function update(UpdateUserRequest $request): void;

    public function delete(int $id): void;
}
