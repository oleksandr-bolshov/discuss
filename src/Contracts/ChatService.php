<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Chat\CreateChatRequest;
use Apathy\Discuss\DataObjects\PaginateByIdRequest;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

interface ChatService
{
    public function paginateChatsByUserId(PaginateByIdRequest $paginationRequest): Paginator;

    public function create(CreateChatRequest $request): void;

    public function chatExists(CreateChatRequest $request): bool;

    public function delete(int $id): void;
}
