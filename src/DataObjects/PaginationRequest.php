<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects;

use Apathy\Discuss\Enum\SortBy;
use Apathy\Discuss\Enum\SortDirection;

final class PaginationRequest
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 15;
    public const DEFAULT_SORT = SortBy::CREATED_AT;
    public const DEFAULT_DIRECTION = SortDirection::DESC;

    public int $id;
    public int $page = self::DEFAULT_PAGE;
    public int $perPage = self::DEFAULT_PER_PAGE;
    public string $sort = self::DEFAULT_SORT;
    public string $direction = self::DEFAULT_DIRECTION;
}
