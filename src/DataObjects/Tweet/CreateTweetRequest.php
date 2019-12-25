<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Tweet;

use Apathy\Discuss\DataObjects\Poll\CreatePollRequest;
use Illuminate\Support\Collection;

final class CreateTweetRequest
{
    public string $text;
    public int $authorId;
    public ?int $inReplyToTweetId;
    public Collection $images;
    public CreatePollRequest $poll;
}
