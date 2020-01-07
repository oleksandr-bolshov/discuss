<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Chat;

use Illuminate\Support\Collection;

final class CreateChatRequest
{
    public Collection $membersIds;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->membersIds = collect($data['members_ids']);
        return $request;
    }
}
