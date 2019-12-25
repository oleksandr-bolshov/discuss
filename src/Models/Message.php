<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Message\CreateMessageRequest;
use Apathy\Discuss\DataObjects\Message\MessageResponse as MessageResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MessageResponse.
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
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

    public function toResponse(): MessageResponse
    {
        $message = new MessageResponse();

        $message->id = (int) $this->id;
        $message->chatId = (int) $this->chat_id;
        $message->text = $this->text;
        $message->isRead = (bool) $this->is_read;
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
