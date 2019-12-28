<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\UserService as UserServiceContract;
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

    public function paginateUsersWhoLikedByTweetId(
        int $tweetId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->whereHas('likes', fn (Builder $query) => $query->where('tweet_id', $tweetId))
                ->orderBy($sort, $direction)
                ->paginate($perPage, ['*'], null, $page)
        );
    }

    public function paginateFollowersByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->findOrFail($userId)
                ->followers()
                ->orderBy($sort, $direction)
                ->paginate($perPage, ['*'], null, $page)
        );
    }

    public function paginateFollowingsByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->findOrFail($userId)
                ->followings()
                ->orderBy($sort, $direction)
                ->paginate($perPage, ['*'], null, $page)
        );
    }

    public function paginateSubscribersByListId(
        int $listId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->whereHas('listsWhereSubscriber', fn (Builder $query) => $query->whereListId($listId))
                ->orderBy($sort, $direction)
                ->paginate($perPage, ['*'], null, $page)
        );
    }

    public function paginateMembersByListId(
        int $listId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            UserModel::withCount('followers')
                ->whereHas('listsWhereMember', fn (Builder $query) => $query->whereListId($listId))
                ->orderBy($sort, $direction)
                ->paginate($perPage, ['*'], null, $page)
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
