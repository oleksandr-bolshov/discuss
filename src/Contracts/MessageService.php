<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Message\CreateMessageRequest;
use Apathy\Discuss\DataObjects\Message\MessageResponse;
use Apathy\Discuss\DataObjects\PaginationRequest;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface MessageService
{
    public function paginateMessagesByChatId(PaginationRequest $paginationRequest): Paginator;

    public function lastMessageByChatId(int $chatId): MessageResponse;

    public function create(CreateMessageRequest $request): void;

    public function read(int $id): void;

    public function delete(int $id): void;
}
