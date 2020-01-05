<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Poll\PollOptionResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PollOptionResponse.
 * @property int $id
 * @property string $option
 * @property int $votes_count
 * @property int $poll_id
 */
final class PollOption extends Model
{
    protected $table = 'poll_options';

    protected $withCount = ['votes'];

    protected $fillable = [
        'option',
        'poll_id',
    ];

    protected $casts = [
        'poll_id' => 'integer',
        'votes_count' => 'integer',
    ];

    public $timestamps = false;

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function toResponse(): PollOptionResponse
    {
        $pollOptionResponse = new PollOptionResponse();
        $pollOptionResponse->id = $this->id;
        $pollOptionResponse->option = $this->option;
        $pollOptionResponse->pollId = $this->poll_id;
        $pollOptionResponse->votesCount = $this->votes_count;

        return $pollOptionResponse;
    }
}
