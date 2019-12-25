<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Like.
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property int $poll_option_id
 * @property PollOption $poll_option
 * @property Carbon $created_at
 */
final class Vote extends Model
{
    public $timestamps = false;

    protected $casts = [
        'user_id' => 'integer',
        'poll_option_id' => 'integer',
    ];

    protected $fillable = [
        'user_id',
        'poll_option_id',
    ];
}
