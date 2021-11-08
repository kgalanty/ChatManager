<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers;
//use WHMCS\Module\Addon\ChatManager\app\Middlewares\AuthMid;
//use WHMCS\Module\Addon\ChatManager\app\Classes\StatsRoleHelper; 
use WHMCS\Module\Addon\ChatManager\app\Classes\AdminGroupsConsts;
abstract class API
{
    //use AuthMid;

    public $params, $input;
    //public static $needAuth;
    public function __construct($params, $input)
    {
        //Vars from module output function
        $this->params = $params;
        //Entire php input variables
        $this->input = $input;
        if(!$_SESSION['adminpw'] || in_array($_SESSION['adminid'], AdminGroupsConsts::AGENT_DISALLOWED)) exit;
    }
}
