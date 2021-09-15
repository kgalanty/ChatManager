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

class AdminGroupsConsts
{
    public const AGENT = [2];
    public const ADMIN = [1, 11];
}
