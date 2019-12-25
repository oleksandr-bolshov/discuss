<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\PollService as PollServiceContract;
use Apathy\Discuss\DataObjects\Poll\HasVotedRequest;
use Apathy\Discuss\DataObjects\Poll\RetractRequest;
use Apathy\Discuss\DataObjects\Poll\VoteRequest;
use Apathy\Discuss\Models\Poll;
use Apathy\Discuss\Models\Poll as PollModel;
use Apathy\Discuss\Models\Vote;
use Apathy\Discuss\Models\Vote as VoteModel;
use Apathy\Discuss\Validators\Poll as PollValidator;
use Carbon\Carbon;

final class PollService implements PollServiceContract
{
    private PollValidator $validator;

    public function __construct(PollValidator $validator)
    {
        $this->validator = $validator;
    }

    public function vote(VoteRequest $request): void
    {
        $this->validator->validateVoteRequest($request);

        $hasVotedRequest = new HasVotedRequest();
        $hasVotedRequest->userId = $request->userId;
        $hasVotedRequest->pollId = $request->pollId;

        if ($this->hasVoted($hasVotedRequest)) {
            return;
        }

        $vote = new VoteModel();
        $vote->user_id = $request->userId;
        $vote->poll_option_id = $request->pollOptionId;
        $vote->created_at = Carbon::now();
        $vote->save();
    }

    public function hasVoted(HasVotedRequest $request): bool
    {
        $this->validator->validatePollRequest($request);

        return Poll::with('options.votes')
            ->find($request->pollId)
            ->options
            ->pluck('votes')
            ->flatten()
            ->contains('user_id', $request->userId);
    }

    public function retract(RetractRequest $request): void
    {
        $this->validator->validateRetractRequest($request);

        VoteModel::where([
            'poll_option_id' => $request->pollOptionId,
            'user_id' => $request->userId,
        ])->delete();
    }

    public function close(int $id): void
    {
        PollModel::whereId($id)->update([
            'end_datetime' => Carbon::now(),
        ]);
    }
}
