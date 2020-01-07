<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

use Illuminate\Support\Collection;

final class CreateUserListRequest
{
    public string $title;
    public ?string $description;
    public int $ownerId;
    public ?Collection $membersIds;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->title = $data['title'];
        $request->description = $data['description'] ?? null;
        $request->ownerId = $data['owner_id'];
        $request->membersIds = isset($data['members_ids']) ? collect($data['members_ids']) : null;

        return $request;
    }
}
