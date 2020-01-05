<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Like.
 * @property int $id
 * @property int $user_id
 * @property int $tweet_id
 * @property Carbon $created_at
 */
final class Like extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tweet_id',
    ];
}
