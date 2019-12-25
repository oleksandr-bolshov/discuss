<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\Poll\HasVotedRequest;
use Apathy\Discuss\DataObjects\Poll\RetractRequest;
use Apathy\Discuss\DataObjects\Poll\VoteRequest;

final class Poll extends Validator
{
    public function validateVoteRequest(VoteRequest $request)
    {
        $this->validate($request, [
            'userId' => 'required|exists:users,id',
            'pollId' => 'required|exists:polls,id',
            'pollOptionId' => 'required|exists:poll_options,id',
        ]);
    }

    public function validatePollRequest(HasVotedRequest $request): void
    {
        $this->validate($request, [
            'userId' => 'required|exists:users,id',
            'pollId' => 'required|exists:polls,id',
        ]);
    }

    public function validateRetractRequest(RetractRequest $request): void
    {
        $this->validate($request, [
            'userId' => 'required|exists:users,id',
            'pollOptionId' => 'required|exists:poll_options,id',
        ]);
    }
}
