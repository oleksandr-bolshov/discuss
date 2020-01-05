<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Message\CreateMessageRequest;
use Apathy\Discuss\DataObjects\Message\MessageResponse as MessageResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MessageResponse.
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property User $user
 * @property string $text
 * @property bool $is_read
 * @property Carbon $created_at
 */
final class Message extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'user_id',
        'text',
        'is_read',
    ];

    protected $casts = [
        'chat_id' => 'integer',
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toResponse(): MessageResponse
    {
        $message = new MessageResponse();

        $message->id = $this->id;
        $message->chatId = $this->chat_id;
        $message->user = $this->user->toResponse();
        $message->text = $this->text;
        $message->isRead = $this->is_read;
        $message->createdAt = Carbon::parse($this->created_at);

        return $message;
    }

    public function fromRequest($message): self
    {
        $this->chat_id = $message->chatId;
        $this->user_id = $message->userId;
        $this->text = $message->text;
        $this->is_read = $message->isRead;

        return $this;
    }

    public static function createFromRequest(CreateMessageRequest $request): self
    {
        return (new self())->fromRequest($request);
    }
}
