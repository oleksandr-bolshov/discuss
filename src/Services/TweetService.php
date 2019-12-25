<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\TweetService as TweetServiceContract;
use Apathy\Discuss\DataObjects\Image\CreateImageRequest;
use Apathy\Discuss\DataObjects\Poll\CreatePollOptionRequest;
use Apathy\Discuss\DataObjects\Tweet\CreateTweetRequest;
use Apathy\Discuss\DataObjects\Tweet\TweetResponse;
use Apathy\Discuss\Enum\ListUserType;
use Apathy\Discuss\Models\Poll as PollModel;
use Apathy\Discuss\Models\PollOption as PollOptionModel;
use Apathy\Discuss\Models\Tweet as TweetModel;
use Apathy\Discuss\Traits\PaginationItemsToEntities;
use Apathy\Discuss\Validators\Tweet as TweetValidator;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;

final class TweetService implements TweetServiceContract
{
    use PaginationItemsToEntities;

    private TweetValidator $validator;

    public function __construct(TweetValidator $validator)
    {
        $this->validator = $validator;
    }

    public function find(int $id): TweetResponse
    {
        return TweetModel::withCount('replies')->findOrFail($id)
            ->withParent()
            ->withReplies()
            ->toResponse();
    }

    public function paginate(
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            TweetModel::withCount('replies')->orderBy($sort, $direction)->paginate($perPage, ['*'], null, $page)
        );
    }

    public function paginateByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            TweetModel::withCount('replies')
                ->where('author_id', $userId)
                ->orderBy($sort, $direction)
                ->paginate($perPage, ['*'], null, $page)
        );
    }

    public function paginateByListId(
        int $listId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        return $this->transformPaginationItems(
            TweetModel::withCount('replies')
                ->whereIn('author_id', fn (Builder $query) => $query->select('user_id')
                        ->from('list_user')
                        ->where('list_id', $listId)
                        ->where('user_type', ListUserType::MEMBER)
                )
                ->orderBy($sort, $direction)
                ->paginate($perPage, ['*'], null, $page)
        );
    }

    public function create(CreateTweetRequest $request): void
    {
        $this->validator->validateCreateRequest($request);

        $tweet = TweetModel::createFromRequest($request);
        $tweet->save();

        if (isset($request->images)) {
            DB::table('images')->insert($request->images->map(
                function (CreateImageRequest $request) use ($tweet) {
                    return [
                        'path' => $request->path,
                        'tweet_id' => $tweet->id,
                    ];
                }
            )->toArray());
        }

        if (isset($request->poll)) {
            $pollModel = new PollModel();
            $pollModel->title = $request->poll->title;
            $pollModel->end_datetime = $request->poll->endDatetime;
            $pollModel->tweet_id = $tweet->id;

            $pollModel->save();

            $pollModel->options()->saveMany(
                $request->poll->options->map(fn (CreatePollOptionRequest $pollOption) => new PollOptionModel([
                        'option' => $pollOption->option,
                    ])
                )
            );
        }
    }

    public function delete(int $id): void
    {
        TweetModel::destroy($id);
    }
}
