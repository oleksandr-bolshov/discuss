<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\MessageService as MessageServiceContract;
use Apathy\Discuss\DataObjects\Message\CreateMessageRequest;
use Apathy\Discuss\DataObjects\Message\MessageResponse;
use Apathy\Discuss\DataObjects\PaginationRequest;
use Apathy\Discuss\Models\Message as MessageModel;
use Apathy\Discuss\Traits\PaginationItemsToEntities;
use Apathy\Discuss\Validators\Message as MessageValidator;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

final class MessageService implements MessageServiceContract
{
    use PaginationItemsToEntities;

    private MessageValidator $validator;

    public function __construct(MessageValidator $validator)
    {
        $this->validator = $validator;
    }

    public function paginateMessagesByChatId(PaginationRequest $paginationRequest): Paginator {
        return $this->transformPaginationItems(
            MessageModel::whereChatId($paginationRequest->id)
                ->orderBy($paginationRequest->sort, $paginationRequest->direction)
                ->paginate($paginationRequest->perPage, ['*'], null, $paginationRequest->page)
        );
    }

    public function lastMessageByChatId(int $chatId): MessageResponse
    {
        return MessageModel::whereChatId($chatId)->latest()->first()->toResponse();
    }

    public function create(CreateMessageRequest $request): void
    {
        $this->validator->validateCreateRequest($request);

        $messageModel = MessageModel::createFromRequest($request);
        $messageModel->created_at = Carbon::now();
        $messageModel->save();
    }

    public function read(int $id): void
    {
        MessageModel::whereId($id)->update([
            'is_read' => true,
        ]);
    }

    public function delete(int $id): void
    {
        MessageModel::destroy($id);
    }
}
