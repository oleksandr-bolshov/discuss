<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests\Unit;

use Apathy\Discuss\Contracts\PollService;
use Apathy\Discuss\DataObjects\Poll\HasVotedRequest;
use Apathy\Discuss\DataObjects\Poll\RetractRequest;
use Apathy\Discuss\DataObjects\Poll\VoteRequest;
use Apathy\Discuss\Models\Poll;
use Apathy\Discuss\Models\PollOption;
use Apathy\Discuss\Models\Tweet;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Models\Vote;
use Apathy\Discuss\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PollServiceTest extends TestCase
{
    use RefreshDatabase;

    private PollService $pollService;
    private int $tweetId;
    private int $pollId;
    private int $pollOptionId;
    private int $userId;

    public function setUp(): void
    {
        parent::setUp();
        $this->pollService = $this->app->make(PollService::class);

        $this->userId = factory(User::class)->create()->id;
        $this->tweetId = factory(Tweet::class)->create()->id;
        $this->pollId = factory(Poll::class)->create()->id;
        $pollOptions = factory(PollOption::class, 2)->create([
            'poll_id' => $this->pollId,
        ]);
        $this->pollOptionId = $pollOptions->first()->id;
    }

    public function test_vote()
    {
        $expected = [
            'user_id' => $this->userId,
            'poll_option_id' => $this->pollOptionId,
        ];

        $voteRequest = new VoteRequest();
        $voteRequest->userId = $this->userId;
        $voteRequest->pollId = $this->pollId;
        $voteRequest->pollOptionId = $this->pollOptionId;

        $this->pollService->vote($voteRequest);

        $this->assertDatabaseHas('votes', $expected);
    }

    public function test_vote_not_unique()
    {
        $expected = [
            'user_id' => $this->userId,
            'poll_option_id' => $this->pollOptionId,
        ];

        factory(Vote::class)->create($expected);

        $expectedVotesCount = DB::table('votes')->count();

        $voteRequest = new VoteRequest();
        $voteRequest->userId = $this->userId;
        $voteRequest->pollId = $this->pollId;
        $voteRequest->pollOptionId = $this->pollOptionId;

        $this->pollService->vote($voteRequest);

        $actualVotesCount = DB::table('votes')->count();

        $this->assertEquals($expectedVotesCount, $actualVotesCount);
    }

    public function test_has_voted_when_true()
    {
        factory(Vote::class)->create([
            'poll_option_id' => $this->pollOptionId,
        ]);

        $hasVotedRequest = new HasVotedRequest();
        $hasVotedRequest->userId = $this->userId;
        $hasVotedRequest->pollId = $this->pollId;

        $this->assertTrue($this->pollService->hasVoted($hasVotedRequest));
    }

    public function test_has_voted_when_false()
    {
        $anotherUserId = factory(User::class)->create()->id;
        $voteThatNotExists = new HasVotedRequest();
        $voteThatNotExists->userId = $anotherUserId;
        $voteThatNotExists->pollId = $this->pollId;

        $this->assertFalse($this->pollService->hasVoted($voteThatNotExists));
    }

    public function test_retract()
    {
        $expected = [
            'user_id' => $this->userId,
            'poll_option_id' => $this->pollOptionId,
        ];

        factory(Vote::class)->create($expected);
        $retractRequest = new RetractRequest();
        $retractRequest->pollOptionId = $this->pollOptionId;
        $retractRequest->userId = $this->userId;

        $this->pollService->retract($retractRequest);

        $this->assertDeleted('votes', $expected);
    }

    public function test_close()
    {
        $this->pollService->close($this->pollId);

        $poll = DB::table('polls')->whereId($this->pollId)->first();

        $this->assertTrue($poll->end_datetime <= Carbon::now()->toDateTimeString());
    }
}
