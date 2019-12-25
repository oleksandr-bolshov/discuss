<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Chat;

use Illuminate\Support\Collection;

final class CreateChatRequest
{
    public Collection $membersIds;
}
