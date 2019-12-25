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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class PollOption extends Model
{
    protected $table = 'poll_options';

    protected $withCount = ['votes'];

    protected $fillable = [
        'option',
        'poll_id',
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
        $pollOptionResponse->pollId = (int) $this->poll_id;
        $pollOptionResponse->voterCount = (int) $this->votes_count;

        return $pollOptionResponse;
    }
}
