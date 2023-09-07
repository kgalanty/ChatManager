<?php
require_once(__DIR__.'/../../../init.php');
if(file_exists(__DIR__.'/vendor/autoload.php') === true)
{
    require_once(__DIR__.'/vendor/autoload.php');
}

use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatHelper;

$livechat = new LiveChatHelper();
$livechat->readRecentChats(['tags' => ['values'=> ['sales']]]);
$cmcount = $_SESSION['cmcount'] ? $_SESSION['cmcount'] : 0;
logActivity('Chat manager inserted '.$_SESSION['cmcount'].' rows');
unset($_SESSION['cmcount']);