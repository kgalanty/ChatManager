<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\DateTimeHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatParsers;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use LiveChat\Api\Client as LiveChat;
use WHMCS\Module\Addon\ChatManager\app\Classes\FindClientHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\FindOrderHelper;
use WHMCS\Database\Capsule as DB;

class MatchClient
{
    public static function execute(array $params = [])
    {
        $client = FindClientHelper::execute($params['customer']);
        if ($client) {
            $orderid = FindOrderHelper::execute($params['customer'], $params['chatitem']->thread->events[0]->created_at);
            if ($orderid) {
                $domain = FindOrderHelper::findDomainByOrder($orderid);
            }
        }
        return ['name' => $client ? $client->firstname.' '.$client->lastname:'', 'email' => $client ? $client->email : '', 'domain' => $domain];
    }
}
