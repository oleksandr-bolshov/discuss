<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\UserService as UserServiceContract;
use Apathy\Discuss\DataObjects\PaginateByIdRequest;
use Apathy\Discuss\DataObjects\User\CreateUserRequest;
use Apathy\Discuss\DataObjects\User\UpdateUserRequest;
use Apathy\Discuss\DataObjects\User\UserResponse;
use Apathy\Discuss\Models\User as UserModel;
use Apathy\Discuss\Traits\PaginationItemsToEntities;
use Apathy\Discuss\Validators\User as UserValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Hash;

final class UserService implements UserServiceContract
{
    use PaginationItemsToEntities;

    private UserValidator $validator;

    public function __construct(UserValidator $validator)
    {
        $this->validator = $validator;
    }

    public function find(int $id): UserResponse
    {
        return UserModel::withCount('followers')->findOrFail($id)->toResponse();
    }

    public function paginateUsersWhoLikedByTweetId(PaginateByIdRequest $paginationRequest): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->whereHas('likes', fn (Builder $query) => $query->where('tweet_id', $paginationRequest->id))
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function paginateFollowersByUserId(PaginateByIdRequest $paginationRequest): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->findOrFail($paginationRequest->id)
                ->followers()
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function paginateFollowingsByUserId(PaginateByIdRequest $paginationRequest): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->findOrFail($paginationRequest->id)
                ->followings()
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function paginateSubscribersByListId(PaginateByIdRequest $paginationRequest): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->whereHas('listsWhereSubscriber', fn (Builder $query) => $query->whereListId($paginationRequest->id))
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function paginateMembersByListId(PaginateByIdRequest $paginationRequest): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->whereHas('listsWhereMember', fn (Builder $query) => $query->whereListId($paginationRequest->id))
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function create(CreateUserRequest $request): void
    {
        $this->validator->validateCreateRequest($request);
        $user = UserModel::createFromRequest($request);
        $user->password = Hash::make($user->password);
        $user->save();
    }

    public function update(UpdateUserRequest $request): void
    {
        $this->validator->validateUpdateRequest($request);
        UserModel::findOrFail($request->id)->fromRequest($request)->save();
    }

    public function delete(int $id): void
    {
        UserModel::destroy($id);
    }
}
