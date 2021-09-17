<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;

class Agents extends API
{
    public function get()
    {
        $action = trim($_GET['a']);
        if($action == 'GetAgentsList')
        {
            $query = trim($_GET['q']);
            $result = Admin::where('firstname', 'LIKE', '%'.$query.'%')->orWhere('lastname', 'LIKE', '%'.$query.'%')
            ->orWhere('username', 'LIKE','%'.$query.'%')->orWhere('email', 'LIKE', '%'.$query.'%')
            ->get();
            return ['data' => $result, 'result' => 'success'];

        }
    }
    public function post()
    {
    }
}
