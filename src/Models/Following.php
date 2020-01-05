<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Following.
 * @property int $id
 * @property int $follower_id
 * @property int $user_id
 * @property Carbon $created_at
 */
final class Following extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'follower_id',
        'user_id',
    ];
}
