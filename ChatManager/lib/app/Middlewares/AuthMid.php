<?php
namespace WHMCS\Module\Addon\ChatManager\app\Middlewares;
use WHMCS\Database\Capsule as DB;
//use WHMCS\Module\Addon\ChatManager\app\Classes\StatsRoleHelper; StatsRoleHelper::getPermID()
trait AuthMid
{
    public function checkPermission(int $permid)
    {
        if(!$permid) return false;
        $roleid = DB::table('tbladmins')->where('id', $_SESSION['adminid'])->value('roleid');
        if(DB::table('tbladminperms')->where('roleid', $roleid)->where('permid', $permid)->count() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}