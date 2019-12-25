<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\Message\CreateMessageRequest;

final class Message extends Validator
{
    public function validateCreateRequest(CreateMessageRequest $request): void
    {
        $this->validate($request, [
            'chatId' => 'required|exists:chats,id',
            'userId' => 'required|exists:users,id',
            'text' => 'required',
            'isRead' => 'required',
        ]);
    }
}
