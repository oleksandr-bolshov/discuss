<?php

use Apathy\Discuss\Enum\ListUserType;
use Apathy\Discuss\Models\User;
use Apathy\Discuss\Models\UserList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListsSeeder extends Seeder
{
    public function run()
    {
        factory(User::class)->create();
        $listsIds = factory(UserList::class, 5)->create()->pluck('id');
        $usersIds = factory(User::class, 200)->create()->pluck('id');

        $listsUsers = [];

        foreach ($listsIds as $listId) {
            foreach ($usersIds as $userId) {
                $listsUsers[] = [
                    'list_id' => $listId,
                    'user_id' => $userId,
                    'user_type' => rand(0, 1) == 0 ? ListUserType::SUBSCRIBER : ListUserType::MEMBER,
                ];
            }
        }

        DB::table('list_user')->insert($listsUsers);
    }
}
