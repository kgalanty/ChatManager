<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\DateTimeHelper;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\FindClientHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\FindOrderHelper;

class LiveChatParsers
{
    public static function findCloseChatDate($eventsList)
    {
        foreach ($eventsList as $event) {
            if ($event->system_message_type == 'manual_archived_agent' || $event->system_message_type == 'manual_archived_customer') {
                return $event->created_at;
            }
        }
        return $eventsList[array_key_last($eventsList)]->created_at;
    }
    public static function parseArchiveList($list)
    {

        foreach ($list as $chatitem) {
            // echo('<pre>'); var_dump($chatitem->thread->tags);die;
            $user = $chatitem->thread->user_ids[count($chatitem->thread->user_ids) - 1];
            // if($chatitem->thread->id == 'QZ20FI9SY7')
            //{
            //}
            //foreach ($chatitem->thread as $thread) {
            // $client = FindClientHelper::execute(LiveChatHelper::getUserById($user, $chatitem->users));
            // if($client)
            // {
            //     $orderid = FindOrderHelper::execute($client, $chatitem->thread->events[0]->created_at);
            //     if($orderid)
            //     {
            //         $domain = self::findDomainByOrder($orderid, $chatitem->thread->events[0]->created_at);
            //     }
            // }

            if (DB::table('chat_threads')->where('threadid', $chatitem->thread->id)->count() == 0) {
                $insertRow = [
                    'chatid' => $chatitem->id,
                    'threadid' => $chatitem->thread->id,
                    'users' => $user,
                    'name' => '',
                    'email' => '',
                    'domain' => '',
                    'orderid' => null,
                    'agent' => $chatitem->thread->user_ids[0],
                    'date' => self::findCloseChatDate($chatitem->thread->events),
                    'created_at' => DB::raw('NOW()')

                ];
                $customer = LiveChatHelper::getUserById($user, $chatitem->users);
                $insertRow = array_merge($insertRow, MatchClient::execute(
                    ['chatitem' => $chatitem, 'customer' => $customer]
                ));

               
                $id = DB::table('chat_threads')->insertGetId($insertRow);
                //echo('<pre>'); var_dump($insertRow, $id, $chatitem, count($list)); die;
                self::parseTags($id, $chatitem->thread->tags);
            }

            self::parseCustomer($user, $chatitem->users);
        }
    }
    public static function parseTags(int $thread_id, array $tags)
    {
        foreach ($tags as $tag) {
            if (DB::table('chat_tags')->where('t_id', $thread_id)->count() == 0) {
                $rows[] = ['t_id' => $thread_id, 'tag' => $tag, 'approved' => 1];
            }
        }

        if ($rows) {
            DB::table('chat_tags')->insert($rows);
        }
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
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'ip' => $customer->last_visit->ip,
                        'user_agent' => $customer->last_visit->user_agent,
                        'geolocation' => $customer->last_visit->geolocation ? json_encode($customer->last_visit->geolocation) : null

                    ]
                );
        }
    }
}
