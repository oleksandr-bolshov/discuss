<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Poll;

use Carbon\Carbon;
use Illuminate\Support\Collection;

final class PollResponse
{
    public int $id;
    public string $title;
    public Carbon $endDatetime;
    public Collection $options;
    public int $tweetId;
}
