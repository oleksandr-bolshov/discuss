<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Apathy\Discuss\DataObjects\UserList\CreateUserListRequest;
use Apathy\Discuss\DataObjects\UserList\MemberRequest;
use Apathy\Discuss\DataObjects\UserList\SubscriberRequest;
use Apathy\Discuss\DataObjects\UserList\UpdateUserListRequest;

final class UserList extends Validator
{
    public function validateCreateListRequest(CreateUserListRequest $request): void
    {
        $this->validate($request, [
            'title' => 'required|max:25',
            'description' => 'nullable',
            'ownerId' => 'required',
            'membersIds' => 'nullable',
            'membersIds.*' => 'required_with:membersIds|exists:users,id',
        ]);
    }

    public function validateUpdateListRequest(UpdateUserListRequest $request): void
    {
        $this->validate($request, [
            'id' => 'required|exists:lists',
            'title' => 'nullable|max:25',
            'description' => 'nullable',
        ]);
    }

    public function validateMemberRequest(MemberRequest $request): void
    {
        $this->validate($request, [
            'listId' => 'required|exists:lists,id',
            'memberId' => 'required|exists:users,id',
        ]);
    }

    public function validateSubscriberRequest(SubscriberRequest $request): void
    {
        $this->validate($request, [
            'listId' => 'required|exists:lists,id',
            'subscriberId' => 'required|exists:users,id',
        ]);
    }
}
