<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\User\CreateUserRequest;
use Apathy\Discuss\DataObjects\User\UserResponse;
use Apathy\Discuss\Enum\ListUserType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class UserResponse.
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string|null $profile_image
 * @property Collection $followers
 * @property int $followers_count
 * @property Collection $followings
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'followers_count' => 'integer',
    ];

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'followings', 'user_id', 'follower_id')->withPivot('created_at');
    }

    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'followings', 'follower_id', 'user_id')->withPivot('created_at');
    }

    public function listsWhereSubscriber(): BelongsToMany
    {
        return $this->belongsToMany(
            UserList::class,
            'list_user',
            'user_id',
            'list_id'
        )->wherePivot('user_type', ListUserType::SUBSCRIBER);
    }

    public function listsWhereMember(): BelongsToMany
    {
        return $this->belongsToMany(
            UserList::class,
            'list_user',
            'user_id',
            'list_id'
        )->wherePivot('user_type', ListUserType::MEMBER);
    }

    public function toResponse(): UserResponse
    {
        $user = new UserResponse();

        $user->id = $this->id;
        $user->firstName = $this->first_name;
        $user->lastName = $this->last_name;
        $user->email = $this->email;
        $user->username = $this->username;
        $user->password = $this->password;
        $user->profileImage = $this->profile_image;
        $user->createdAt = $this->created_at;
        $user->updatedAt = $this->updated_at;

        if ($this->followers_count !== null) {
            $user->followersCount = $this->followers_count;
        }

        return $user;
    }

    public function fromRequest($request): self
    {
        $this->first_name = $request->firstName ?? $this->first_name;
        $this->last_name = $request->lastName ?? $this->last_name;
        $this->email = $request->email ?? $this->email;
        $this->username = $request->username ?? $this->username;
        $this->password = $request->password ?? $this->password;
        $this->profile_image = $request->profileImage ?? $this->profile_image;

        return $this;
    }

    public static function createFromRequest(CreateUserRequest $request): self
    {
        return (new self())->fromRequest($request);
    }
}
