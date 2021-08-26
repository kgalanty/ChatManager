<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\StatsRoleHelper;
class Auth extends API
{
    public function get()
    {
        $data['stats'] = StatsRoleHelper::canEnterSTats() ? 1 : 0;
        //$data['stats'] = 1;
        return ['results' => $data];
    }
    public function post()
    {
    }
}
