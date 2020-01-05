<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\PaginationRequest;
use Apathy\Discuss\DataObjects\UserList\CreateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\MemberRequest;
use Apathy\Discuss\DataObjects\UserList\SubscriberRequest;
use Apathy\Discuss\DataObjects\UserList\UpdateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\UserListResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface ListService
{
    public function paginateByOwnerId(PaginationRequest $paginationRequest): Paginator;

    public function paginateBySubscriberId(PaginationRequest $paginationRequest): Paginator;

    public function paginateByMemberId(PaginationRequest $paginationRequest): Paginator;

    public function find(int $id): UserListResponse;

    public function create(CreateUserListRequest $request): void;

    public function addSubscriber(SubscriberRequest $request): void;

    public function addMember(MemberRequest $request): void;

    public function hasSubscriber(SubscriberRequest $request): bool;

    public function hasMember(MemberRequest $request): bool;

    public function update(UpdateUserListRequest $request): void;

    public function delete(int $id): void;

    public function removeSubscriber(SubscriberRequest $request): void;

    public function removeMember(MemberRequest $request): void;
}
