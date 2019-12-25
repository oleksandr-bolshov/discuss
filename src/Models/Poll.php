<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Poll\PollResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PollResponse.
 * @property int $id
 * @property string $title
 * @property Carbon $end_datetime
 * @property Collection $options
 * @property int $tweet_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Poll extends Model
{
    protected $with = ['options'];

    protected $fillable = [
        'title',
        'end_datetime',
        'tweet_id',
    ];

    public $timestamps = false;

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function toResponse(): PollResponse
    {
        $pollResponse = new PollResponse();
        $pollResponse->id = $this->id;
        $pollResponse->title = $this->title;
        $pollResponse->endDatetime = Carbon::parse($this->end_datetime);
        $pollResponse->tweetId = (int) $this->tweet_id;
        $pollResponse->options = $this->options->map->toResponse()->toBase();

        return $pollResponse;
    }
}
