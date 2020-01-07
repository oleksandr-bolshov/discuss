<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\Enum\SortBy;
use Apathy\Discuss\Enum\SortDirection;

interface Pagination
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 15;
    public const DEFAULT_SORT = SortBy::CREATED_AT;
    public const DEFAULT_DIRECTION = SortDirection::DESC;
}
