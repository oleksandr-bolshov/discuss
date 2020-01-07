<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Poll;

use Carbon\Carbon;
use Illuminate\Support\Collection;

final class CreatePollRequest
{
    public string $title;
    public Carbon $endDatetime;
    public Collection $options;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->title = $data['title'];
        $request->endDatetime = Carbon::parse($data['end_datetime']);
        $request->options = collect($data['options']);
        return $request;
    }
}
