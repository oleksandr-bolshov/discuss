<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\UserList\CreateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\UserListResponse as UserListResponse;
use Apathy\Discuss\Enum\ListUserType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class UserListResponse.
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $owner_id
 * @property User $owner
 * @property int $subscribers_count
 * @property int $members_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class UserList extends Model
{
    protected $table = 'lists';

    protected $fillable = [
        'title',
        'description',
        'owner_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'list_user',
            'list_id',
            'user_id'
        )->wherePivot('user_type', ListUserType::MEMBER);
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'list_user',
            'list_id',
            'user_id'
        )->wherePivot('user_type', ListUserType::SUBSCRIBER);
    }

    public function toResponse(): UserListResponse
    {
        $list = new UserListResponse();

        $list->id = (int) $this->id;
        $list->title = $this->title;
        $list->description = $this->description;
        $list->owner = $this->owner->toResponse();
        $list->membersCount = (int) $this->members_count;
        $list->subscribersCount = (int) $this->subscribers_count;
        $list->createdAt = $this->created_at;
        $list->updatedAt = $this->updated_at;

        return $list;
    }

    public function fromRequest($request): self
    {
        $this->title = $request->title ?? $this->title;
        $this->description = $request->description ?? $this->description;
        $this->owner_id = $request->ownerId ?? $this->owner_id;

        return $this;
    }

    public static function createFromRequest(CreateUserListRequest $request): self
    {
        return (new self)->fromRequest($request);
    }
}
