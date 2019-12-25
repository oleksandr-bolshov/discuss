<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Chat\ChatResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ChatResponse.
 * @property int $id
 * @property Collection $members
 * @property Collection $messages
 * @property Carbon $created_at
 */
final class Chat extends Model
{
    public $timestamps = false;

    protected $with = ['members'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('created_at');
    }

    public function toResponse(): ChatResponse
    {
        $chat = new ChatResponse();
        $chat->id = $this->id;
        $chat->members = $this->members->map->toResponse();
        $chat->createdAt = Carbon::parse($this->created_at);

        return $chat;
    }
}
