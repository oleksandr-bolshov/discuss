<?php

declare(strict_types=1);

namespace Apathy\Discuss\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as PaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

trait PaginationItemsToEntities
{
    public function transformPaginationItems(PaginatorContract $paginator): Paginator
    {
        return new Paginator(
            $paginator->toBase()->map->toResponse(),
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage()
        );
    }
}
