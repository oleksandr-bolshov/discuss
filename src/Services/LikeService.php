<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\LikeService as LikeServiceContract;
use Apathy\Discuss\DataObjects\Like\LikeRequest;
use Apathy\Discuss\Models\Like as LikeModel;
use Apathy\Discuss\Validators\Like as LikeValidator;
use Carbon\Carbon;

final class LikeService implements LikeServiceContract
{
    private LikeValidator $validator;

    public function __construct(LikeValidator $validator)
    {
        $this->validator = $validator;
    }

    public function like(LikeRequest $request): void
    {
        $this->validator->validateLikeRequest($request);

        if ($this->isLikes($request)) {
            return;
        }

        $likeModel = new LikeModel();
        $likeModel->user_id = $request->userId;
        $likeModel->tweet_id = $request->tweetId;
        $likeModel->created_at = Carbon::now();
        $likeModel->save();
    }

    public function isLikes(LikeRequest $request): bool
    {
        $this->validator->validateLikeRequest($request);

        return LikeModel::where([
            'tweet_id' => $request->tweetId,
            'user_id' => $request->userId,
        ])->exists();
    }

    public function unlike(LikeRequest $request): void
    {
        $this->validator->validateLikeRequest($request);

        LikeModel::where([
            'tweet_id' => $request->tweetId,
            'user_id' => $request->userId,
        ])->delete();
    }
}
