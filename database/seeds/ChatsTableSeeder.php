<?php

use Apathy\Discuss\Models\Chat;
use Apathy\Discuss\Models\Message;
use Apathy\Discuss\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatsTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        // Step #1. Create chats and add two members for every chat
        $chatUserRelation = [];
        $chats = [];
        $chatId = 1;
        $now = Carbon::now();

        $users->map(function (User $member1) use ($users, &$chatUserRelation, &$chats, &$chatId, $now) {
            $otherUsers = $users->whereNotIn('id', $member1->id);

            $otherUsers->map(function (User $member2) use ($member1, &$chatUserRelation, &$chats, &$chatId, $now) {
                $chatUserRelation[] = [
                    'chat_id' => $chatId,
                    'user_id' => $member1->id,
                    'created_at' => $now,
                ];
                $chatUserRelation[] = [
                    'chat_id' => $chatId,
                    'user_id' => $member2->id,
                    'created_at' => $now,
                ];
                $chats[] = [
                    'id' => $chatId,
                    'created_at' => $now,
                ];
                $chatId++;
            });

            $users->shift();
        });

        DB::table('chats')->insert($chats);
        DB::table('chat_user')->insert($chatUserRelation);

        // Step #2. Fill chats with messages
        $chats = Chat::with('members')->get();

        $messages = $chats->map(function (Chat $chat) {
            return factory(Message::class, 30)->make([
                'chat_id' => $chat->id,
            ])->each(function (Message $message) use ($chat) {
                $message->user_id = $chat->members->random()->id;
            });
        });

        DB::table('messages')->insert($messages->flatten()->toArray());
    }
}
