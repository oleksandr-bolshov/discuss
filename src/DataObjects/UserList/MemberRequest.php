<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

final class MemberRequest
{
    public int $listId;
    public int $memberId;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->listId = $data['list_id'];
        $request->memberId = $data['member_id'];

        return $request;
    }
}
