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
    public const AGENT = [2,22];
    public const ADMIN = [1, 11];

    /*
    Below is the list of Admin IDs disallowed from access the module
    */
    public const AGENT_DISALLOWED = [272];

    /*
    * * Admin ID => Tag
    * Used to display other operator's tags for operator whose tag is present - without edition permission
    */
    public const TAGSAGENTMAP = [153 => 'georgistatev',
     216 => 'Elvira', 
     238=>'deni', 
     230=>'deni', 
     213=>'Ivaylo', 
     263=>'AlexP', 
     267=>'Emily',
    ];
}
