<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\ChatService as ChatServiceContract;
use Apathy\Discuss\Contracts\MessageService as MessageServiceContract;
use Apathy\Discuss\DataObjects\Chat\CreateChatRequest;
use Apathy\Discuss\Models\Chat as ChatModel;
use Apathy\Discuss\Validators\Chat as ChatValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

final class ChatService implements ChatServiceContract
{
    private MessageServiceContract $messageService;
    private ChatValidator $validator;

    public function __construct(
        MessageServiceContract $messageService,
        ChatValidator $validator
    ) {
        $this->messageService = $messageService;
        $this->validator = $validator;
    }

    public function paginateChatsByUserId(
        int $userId,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sort = self::DEFAULT_SORT,
        string $direction = self::DEFAULT_DIRECTION
    ): Paginator {
        $paginator = ChatModel::join('messages', 'chats.id', '=', 'messages.chat_id')
            ->orderBy('messages.'.$sort, $direction)
            ->paginate($perPage, ['*'], null, $page);

        return new Paginator(
            Collection::make($paginator->items())
                ->map(function (ChatModel $chatModel) {
                    $chat = $chatModel->toResponse();
                    $chat->lastMessage = $this->messageService->lastMessageByChatId($chat->id);

                    return $chat;
                }),
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage()
        );
    }

    public function create(CreateChatRequest $request): void
    {
        $this->validator->validateCreateRequest($request);

        if ($this->chatExists($request)) {
            return;
        }

        $chatModel = new ChatModel();
        $chatModel->created_at = Carbon::now();
        $chatModel->save();

        $chatModel->members()->sync($request->membersIds->toArray());
    }

    public function chatExists(CreateChatRequest $request): bool
    {
        return ChatModel::whereHas('members', fn (Builder $query) => $query->whereIn('users.id', $request->membersIds->toArray())
        )->exists();
    }

    public function delete(int $id): void
    {
        ChatModel::destroy($id);
    }
}
