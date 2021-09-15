<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\DateTimeHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatParsers;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use LiveChat\Api\Client as LiveChat;
use WHMCS\Database\Capsule as DB;

class FindClientHelper
{
    public static function execute($user)
    {
        $currentCustomer = DB::table('tblclients')->where('email', $user->email)->first();
        return $currentCustomer;
    }
}