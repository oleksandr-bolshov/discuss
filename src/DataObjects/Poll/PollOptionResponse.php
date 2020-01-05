<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Poll;

final class PollOptionResponse
{
    public int $id;
    public string $option;
    public int $votesCount;
    public int $pollId;
}
