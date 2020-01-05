<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\ListService as ListServiceContract;
use Apathy\Discuss\DataObjects\PaginationRequest;
use Apathy\Discuss\DataObjects\UserList\CreateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\MemberRequest;
use Apathy\Discuss\DataObjects\UserList\SubscriberRequest;
use Apathy\Discuss\DataObjects\UserList\UpdateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\UserListResponse;
use Apathy\Discuss\Enum\ListUserType;
use Apathy\Discuss\Models\UserList as UsersListModel;
use Apathy\Discuss\Traits\PaginationItemsToEntities;
use Apathy\Discuss\Validators\UserList as UserListValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

final class ListService implements ListServiceContract
{
    use PaginationItemsToEntities;

    private UserListValidator $validator;

    public function __construct(UserListValidator $validator)
    {
        $this->validator = $validator;
    }

    public function find(int $id): UserListResponse
    {
        return UsersListModel::with('owner')
            ->withCount('subscribers', 'members')
            ->findOrFail($id)
            ->toResponse();
    }

    public function paginateByOwnerId(PaginationRequest $paginationRequest): Paginator
    {
        return $this->transformPaginationItems(
            UsersListModel::with('owner')
                ->withCount('subscribers', 'members')
                ->where('owner_id', $paginationRequest->id)
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function paginateBySubscriberId(PaginationRequest $paginationRequest): Paginator
    {
        return $this->transformPaginationItems(
            UsersListModel::with('owner')
                ->withCount('subscribers', 'members')
                ->whereHas('subscribers', fn (Builder $query) => $query->where('user_id', $paginationRequest->id)
                        ->where('user_type', ListUserType::SUBSCRIBER)
                )
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function paginateByMemberId(PaginationRequest $paginationRequest): Paginator
    {
        return $this->transformPaginationItems(
            UsersListModel::with('owner')
                ->withCount('subscribers', 'members')
                ->whereHas('members', fn (Builder $query) => $query->where('user_id', $paginationRequest->id)
                        ->where('user_type', ListUserType::MEMBER)
                )
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function create(CreateUserListRequest $request): void
    {
        $this->validator->validateCreateListRequest($request);

        $listModel = UsersListModel::createFromRequest($request);
        $listModel->save();

        if (isset($request->membersIds)) {
            $listModel->members()->sync(
                $request->membersIds->combine(
                    collect()->pad(
                        $request->membersIds->count(),
                        ['user_type' => ListUserType::MEMBER]
                    )
                )
            );
        }
    }

    public function addSubscriber(SubscriberRequest $request): void
    {
        $this->validator->validateSubscriberRequest($request);

        if ($this->hasSubscriber($request)) {
            return;
        }

        $list = UsersListModel::findOrFail($request->listId);
        $list->subscribers()->attach($request->subscriberId, [
            'user_type' => ListUserType::SUBSCRIBER,
        ]);
    }

    public function addMember(MemberRequest $request): void
    {
        $this->validator->validateMemberRequest($request);

        if ($this->hasMember($request)) {
            return;
        }

        $list = UsersListModel::findOrFail($request->listId);
        $list->members()->attach($request->memberId, [
            'user_type' => ListUserType::MEMBER,
        ]);
    }

    public function hasSubscriber(SubscriberRequest $request): bool
    {
        return UsersListModel::whereHas(
            'subscribers', fn (Builder $query) => $query->where('users.id', $request->subscriberId)
        )
            ->where('lists.id', $request->listId)
            ->exists();
    }

    public function hasMember(MemberRequest $request): bool
    {
        return UsersListModel::whereHas(
            'members', fn (Builder $query) => $query->where('users.id', $request->memberId)
        )
            ->where('lists.id', $request->listId)
            ->exists();
    }

    public function update(UpdateUserListRequest $request): void
    {
        $this->validator->validateUpdateListRequest($request);

        UsersListModel::findOrFail($request->id)->fromRequest($request)->save();
    }

    public function delete(int $id): void
    {
        UsersListModel::destroy($id);
    }

    public function removeSubscriber(SubscriberRequest $request): void
    {
        $this->validator->validateSubscriberRequest($request);

        $list = UsersListModel::findOrFail($request->listId);
        $list->subscribers()->detach($request->subscriberId);
    }

    public function removeMember(MemberRequest $request): void
    {
        $this->validator->validateMemberRequest($request);

        $list = UsersListModel::findOrFail($request->listId);
        $list->members()->detach($request->memberId);
    }
}
