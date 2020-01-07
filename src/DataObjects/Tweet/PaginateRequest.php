<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Tweet;

use Apathy\Discuss\Contracts\Pagination;

final class PaginateRequest implements Pagination
{
    public int $page = self::DEFAULT_PAGE;
    public int $perPage = self::DEFAULT_PER_PAGE;
    public string $sort = self::DEFAULT_SORT;
    public string $direction = self::DEFAULT_DIRECTION;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->page = $data['page'] ?? $request->page;
        $request->perPage = $data['per_page'] ?? $request->perPage;
        $request->sort = $data['sort'] ?? $request->sort;
        $request->direction = $data['direction'] ?? $request->direction;
        return $request;
    }
}
