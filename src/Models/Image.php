<?php

declare(strict_types=1);

namespace Apathy\Discuss\Models;

use Apathy\Discuss\DataObjects\Image\ImageResponse;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ImageResponse.
 * @property int $id
 * @property string $path
 * @property int $tweet_id
 */
final class Image extends Model
{
    protected $fillable = [
        'path',
        'tweet_id',
    ];

    protected $casts = [
        'tweet_id' => 'integer',
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
