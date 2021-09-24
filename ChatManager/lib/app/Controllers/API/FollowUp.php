<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;
use WHMCS\Module\Addon\ChatManager\app\Models\Followup as FollowupModel;
class Followup extends API
{
    public function get()
    {
        
            
    }
    public function post()
    {
        $threadid = intval($this->input['threadid']);
        FollowupModel::create(
            [
                'threadid' => $threadid,
                'followupdate' => gmdate('Y-m-d H:i:s'),
                'doer' => $_SESSION['adminid']
            ]
            );
        return ['results' => 'success'];
    }
}
