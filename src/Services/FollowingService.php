<?php

declare(strict_types=1);

namespace Apathy\Discuss\Services;

use Apathy\Discuss\Contracts\FollowingService as FollowerServiceContract;
use Apathy\Discuss\DataObjects\Following\FollowingRequest;
use Apathy\Discuss\Models\Following as FollowingModel;
use Apathy\Discuss\Validators\Following as FollowingValidator;
use Carbon\Carbon;

final class FollowingService implements FollowerServiceContract
{
    private FollowingValidator $validator;

    public function __construct(FollowingValidator $validator)
    {
        $this->validator = $validator;
    }

    public function follow(FollowingRequest $request): void
    {
        $this->validator->validateFollowingRequest($request);

        if ($this->isFollows($request)) {
            return;
        }

        $follower = new FollowingModel();
        $follower->follower_id = $request->followerId;
        $follower->user_id = $request->userId;
        $follower->created_at = Carbon::now();
        $follower->save();
    }

    public function isFollows(FollowingRequest $request): bool
    {
        $this->validator->validateFollowingRequest($request);

        return FollowingModel::where([
            'follower_id' => $request->followerId,
            'user_id' => $request->userId,
        ])->exists();
    }

    public function unfollow(FollowingRequest $request): void
    {
        $this->validator->validateFollowingRequest($request);

        FollowingModel::where([
            'follower_id' => $request->followerId,
            'user_id' => $request->userId,
        ])->delete();
    }
}
