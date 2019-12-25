<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\Tweet\CreateTweetRequest;

final class Tweet extends Validator
{
    private const MIN_POLL_OPTIONS_AMOUNT = 2;

    public function validateCreateRequest(CreateTweetRequest $request): void
    {
        $this->validate($request, [
            'text' => 'required',
            'authorId' => 'required|exists:users,id',
            'inReplyToTweetId' => 'nullable|exists:tweets,id',
            'images' => 'nullable',
            'images.*.path' => 'required_with:images|required',
            'poll' => 'nullable',
            'poll.title' => 'required_with:poll|max:25',
            'poll.endDatetime' => 'required_with:poll|date',
            'poll.options' => 'required_with:poll|array|min:'.self::MIN_POLL_OPTIONS_AMOUNT,
            'poll.options.*.option' => 'required_with:poll|max:25',
        ]);
    }
}
