<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Tweet;

use Apathy\Discuss\DataObjects\Poll\CreatePollRequest;
use Illuminate\Support\Collection;

final class CreateTweetRequest
{
    public string $text;
    public int $authorId;
    public ?int $inReplyToTweetId;
    public ?Collection $images;
    public ?CreatePollRequest $poll;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->text = $data['text'];
        $request->authorId = $data['author_id'];
        $request->inReplyToTweetId = $data['parent_id'] ?? null;
        $request->images = isset($data['images']) ? collect($data['images']) : null;
        $request->poll = isset($data['poll']) ? CreatePollRequest::fromArray($data['poll']) : null;

        return $request;
    }
}
