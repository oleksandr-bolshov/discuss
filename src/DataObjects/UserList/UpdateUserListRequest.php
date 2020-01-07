<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\UserList;

final class UpdateUserListRequest
{
    public int $id;
    public ?string $title;
    public ?string $description;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->id = $data['id'];
        $request->title = $data['title'] ?? null;
        $request->description = $data['description'] ?? null;
        return $request;
    }
}
