<?php

use WHMCS\Module\Addon\ChatManager\Dispatcher;
use WHMCS\Module\Addon\ChatManager\app\Addon;
use WHMCS\Module\Addon\ChatManager\app\ClientAreaDispatcher;
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
if(file_exists(__DIR__.'/vendor/autoload.php') === true)
{
    require_once(__DIR__.'/vendor/autoload.php');
}
function ChatManager_config()
{
    return Addon::config();
}
function ChatManager_output($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $ctrl = isset($_REQUEST['c']) ? $_REQUEST['c'] : 'home';
    $dispatcher = new Dispatcher();
    $response = $dispatcher->dispatch($ctrl, $action, $vars);
    echo $response;
    exit;
}

function ChatManager_activate()
{
    return Addon::activate();
}
function ChatManager_deactivate()
{
    return Addon::deactivate();
}
function ChatManager_upgrade()
{
    return Addon::upgrade();
}
function ChatManager_clientarea($vars)
{
    echo ClientAreaDispatcher::processRequest($vars);
    die('test');
}