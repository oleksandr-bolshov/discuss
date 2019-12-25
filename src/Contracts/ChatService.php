<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Chat\CreateChatRequest;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface ChatService extends Pagination
{
    public function paginateChatsByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function create(CreateChatRequest $request): void;

    public function chatExists(CreateChatRequest $request): bool;

    public function delete(int $id): void;
}
