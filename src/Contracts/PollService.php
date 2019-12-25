<?php

declare(strict_types=1);

namespace Apathy\Discuss\Contracts;

use Apathy\Discuss\DataObjects\Poll\HasVotedRequest;
use Apathy\Discuss\DataObjects\Poll\RetractRequest;
use Apathy\Discuss\DataObjects\Poll\VoteRequest;

interface PollService
{
    public function vote(VoteRequest $request): void;

    public function hasVoted(HasVotedRequest $request): bool;

    public function retract(RetractRequest $request): void;

    public function close(int $id): void;
}
