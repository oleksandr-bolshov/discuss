<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Image\ImageResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ImageResponse.
 * @property int $id
 * @property string $path
 * @property int $tweet_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Image extends Model
{
    protected $fillable = [
        'path',
        'tweet_id',
    ];

    public $timestamps = false;

    public function toResponse(): ImageResponse
    {
        $imageResponse = new ImageResponse();
        $imageResponse->id = $this->id;
        $imageResponse->path = $this->path;
        $imageResponse->tweetId = (int) $this->tweet_id;

        return $imageResponse;
    }
}
