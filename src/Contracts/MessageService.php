<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Message\CreateMessageRequest;
use Apathy\Discuss\DataObjects\Message\MessageResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface MessageService extends Pagination
{
    public function paginateMessagesByChatId(
        int $chatId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator;

    public function lastMessageByChatId(int $chatId): MessageResponse;

    public function create(CreateMessageRequest $request): void;

    public function read(int $id): void;

    public function delete(int $id): void;
}
