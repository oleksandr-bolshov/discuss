<?php

declare(strict_types=1);

namespace Apathy\Discuss;

use Apathy\Discuss\Contracts\ChatService as ChatServiceContract;
use Apathy\Discuss\Contracts\FollowingService as FollowingServiceContract;
use Apathy\Discuss\Contracts\LikeService as LikeServiceContract;
use Apathy\Discuss\Contracts\ListService as ListServiceContract;
use Apathy\Discuss\Contracts\MessageService as MessageServiceContract;
use Apathy\Discuss\Contracts\PollService as PollServiceContract;
use Apathy\Discuss\Contracts\TweetService as TweetServiceContract;
use Apathy\Discuss\Contracts\UserService as UserServiceContract;
use Apathy\Discuss\Services\ChatService;
use Apathy\Discuss\Services\FollowingService;
use Apathy\Discuss\Services\LikeService;
use Apathy\Discuss\Services\ListService;
use Apathy\Discuss\Services\MessageService;
use Apathy\Discuss\Services\PollService;
use Apathy\Discuss\Services\TweetService;
use Apathy\Discuss\Services\UserService;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;

final class DiscussServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserServiceContract::class, UserService::class);
        $this->app->bind(TweetServiceContract::class, TweetService::class);
        $this->app->bind(PollServiceContract::class, PollService::class);
        $this->app->bind(LikeServiceContract::class, LikeService::class);
        $this->app->bind(FollowingServiceContract::class, FollowingService::class);
        $this->app->bind(ChatServiceContract::class, ChatService::class);
        $this->app->bind(MessageServiceContract::class, MessageService::class);
        $this->app->bind(ListServiceContract::class, ListService::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
            __DIR__.'/../database/seeds' => database_path('seeds'),
        ]);

        $this->app->make(Factory::class)->load(__DIR__.'/../database/factories');
    }
}
