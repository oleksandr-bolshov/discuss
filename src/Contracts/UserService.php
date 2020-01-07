<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\PaginateByIdRequest;
use Apathy\Discuss\DataObjects\User\CreateUserRequest;
use Apathy\Discuss\DataObjects\User\UpdateUserRequest;
use Apathy\Discuss\DataObjects\User\UserResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface UserService
{
    public function find(int $id): UserResponse;

    public function paginateUsersWhoLikedByTweetId(PaginateByIdRequest $paginationRequest): Paginator;

    public function paginateFollowersByUserId(PaginateByIdRequest $paginationRequest): Paginator;

    public function paginateFollowingsByUserId(PaginateByIdRequest $paginationRequest): Paginator;

    public function paginateSubscribersByListId(PaginateByIdRequest $paginationRequest): Paginator;

    public function paginateMembersByListId(PaginateByIdRequest $paginationRequest): Paginator;

    public function create(CreateUserRequest $request): void;

    public function update(UpdateUserRequest $request): void;

    public function delete(int $id): void;
}
