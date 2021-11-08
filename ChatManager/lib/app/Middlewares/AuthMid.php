<?php
namespace WHMCS\Module\Addon\ChatManager\app\Middlewares;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;
use WHMCS\Module\Addon\ChatManager\app\Classes\AdminGroupsConsts;
//use WHMCS\Module\Addon\ChatManager\app\Classes\StatsRoleHelper; StatsRoleHelper::getPermID()
trait AuthMid
{
    public function checkPermission()
    {
        if(AuthControl::isAdmin() || AuthControl::isAgent() || !in_array($_SESSION['adminid'], AdminGroupsConsts::AGENT_DISALLOWED))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}