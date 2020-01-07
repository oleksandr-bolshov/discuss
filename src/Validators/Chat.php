<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\Chat\CreateChatRequest;

final class Chat extends Validator
{
    public function validateCreateRequest(CreateChatRequest $request): void
    {
        $this->validate($request, [
            'membersIds' => 'required|array|size:2',
            'membersIds.*' => 'required|integer|exists:users,id',
        ]);
    }
}
