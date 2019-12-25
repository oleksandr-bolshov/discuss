<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Tweet;

use Apathy\Discuss\DataObjects\Poll\PollResponse;
use Apathy\Discuss\DataObjects\User\UserResponse;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class TweetResponse
{
    public int $id;
    public string $text;
    public UserResponse $author;
    public Collection $replies;
    public int $repliesCount;
    public int $likesCount;
    public ?TweetResponse $inReplyToTweet;
    public ?Collection $images;
    public ?PollResponse $poll;
    public Carbon $createdAt;
    public Carbon $updatedAt;
}
