<?php
require_once(__DIR__.'/../../../init.php');
if(file_exists(__DIR__.'/vendor/autoload.php') === true)
{
    require_once(__DIR__.'/vendor/autoload.php');
}

use WHMCS\Module\Addon\ChatManager\app\Classes\Cron\OrderCron;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$cron = new OrderCron(['timezone' => date_default_timezone_get()]);
$cron->run();
//$livechat->readRecentChats(['tags' => ['values'=> ['sales']]]);
