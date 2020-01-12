<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Tweet\CreateTweetRequest;
use Apathy\Discuss\DataObjects\Tweet\TweetResponse as TweetResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Tweet.
 * @property int $id
 * @property string $text
 * @property int $author_id
 * @property User $author
 * @property Collection $replies
 * @property int $replies_count
 * @property int $likes_count
 * @property Poll|null $poll
 * @property Collection|null $images
 * @property int|null $parent_id
 * @property Tweet|null $parent
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Tweet extends Model
{
    protected $with = [
        'author',
        'poll',
        'images',
    ];

    protected $withCount = [
        'likes',
    ];

    protected bool $withParent = false;

    protected bool $withReplies = false;

    protected $fillable = [
        'text',
        'author_id',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'replies_count' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function poll(): HasOne
    {
        return $this->hasOne(Poll::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function toResponse(): TweetResponse
    {
        $tweet = new TweetResponse();

        $tweet->id = $this->id;
        $tweet->text = $this->text;
        $tweet->author = $this->author->toResponse();
        $tweet->repliesCount = $this->replies_count;
        $tweet->likesCount = $this->likes_count;
        $tweet->poll = $this->poll ? $this->poll->toResponse() : null;
        $tweet->images = $this->images ? $this->images->map->toResponse()->toBase() : null;
        $tweet->createdAt = $this->created_at;
        $tweet->updatedAt = $this->updated_at;

        if ($this->withParent) {
            $parent = $this->parent()->withCount('replies')->first();
            if ($parent) {
                $tweet->inReplyToTweet = $parent->toResponse();
            }
        } else {
            $tweet->inReplyToTweet = null;
        }

        if ($this->withReplies) {
            $tweet->replies = $this->replies->map->toResponse()->toBase();
        } else {
            $tweet->replies = null;
        }

        return $tweet;
    }

    public function withParent(): self
    {
        $this->withParent = true;

        return $this;
    }

    public function withReplies(): self
    {
        $this->withReplies = true;

        return $this;
    }

    public function fromRequest($request): self
    {
        $this->text = $request->text ?? $this->text;
        $this->author_id = $request->authorId ?? $this->author_id;
        $this->parent_id = $request->parentTweetId ?? $this->parent_id;

        return $this;
    }

    public static function createFromRequest(CreateTweetRequest $request): self
    {
        return (new self())->fromRequest($request);
    }
}
