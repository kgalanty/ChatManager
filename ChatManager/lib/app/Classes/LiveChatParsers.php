<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\DateTimeHelper;
use WHMCS\Database\Capsule as DB;

class LiveChatParsers
{
    public static function parseArchiveList($list)
    {
        foreach ($list as $chatitem) {
            $user = $chatitem->thread->user_ids[count($chatitem->thread->user_ids) - 1];
            //foreach ($chatitem->thread as $thread) {
            if (DB::table('chat_threads')->where('threadid', $chatitem->thread->id)->count() == 0) {
                $insertRow = [
                    'chatid' => $chatitem->id,
                    'threadid' => $chatitem->thread->id,
                    'users' => $user,
                    'domain' => '',
                    'agent' => $chatitem->thread->user_ids[0],
                    'date' => $chatitem->thread->created_at,
                    'created_at' => DB::raw('NOW()')

                ];
                $id = DB::table('chat_threads')->insertGetId($insertRow);
            self::parseTags($id, $chatitem->thread->tags);
            }

            
            self::parseCustomer($user, $chatitem->users);
            // }
        }
    }
    public static function parseTags(string $thread_id, ?array $tags)
    {
        foreach ($tags as $tag) {
            if (DB::table('chat_tags')->where('t_id', $thread_id)->count() == 0) {
                $rows[] = ['t_id' => $thread_id, 'tag' => $tag];
            }
        }
        DB::table('chat_tags')->insert($rows);
    }
    public static function parseCustomer(string $userid, array $users)
    {
        $customer = LiveChatHelper::getUserById($userid,  $users);
        if ($customer) {
            DB::table('chat_customers')
                ->updateOrInsert(
                    [
                        'client_id' => $userid,
                    ],
                    [

                        'ip' => $customer->last_visit->ip,
                        'user_agent' => $customer->last_visit->user_agent,
                        'geolocation' => json_encode($customer->last_visit->geolocation)

                    ]
                );
        }
    }
}
