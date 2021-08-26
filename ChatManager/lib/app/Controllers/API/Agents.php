<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Models\Request as RequestModel;
use WHMCS\Module\Addon\ChatManager\app\Models\CancelRequest;
use WHMCS\Module\Addon\ChatManager\app\Models\Clientgroup;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;
use WHMCS\Module\Addon\ChatManager\app\Models\InvoiceProlong;
use WHMCS\Module\Addon\ChatManager\app\Models\Service;
use WHMCS\Module\Addon\ChatManager\app\Classes\EmailTemplatesConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\GidsConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\EmailHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\Invoices as InvoicesClass;
use WHMCS\Module\Addon\ChatManager\app\Models\Note;

class Agents extends API
{
    public function get()
    {
        if($_GET['self'])
        {
            return ['data' => $_SESSION['adminid']];
        }
       $results = DB::table('kg_cancelrequests as c')
       ->join('tbladmins as a', 'a.id', '=', 'c.agent')
       ->groupBy('c.agent')
       ->orderBy('a.firstname', 'ASC')
       ->get(['a.id', 'a.firstname', 'a.lastname']);
        return ['data' => $results];
    //     $results = DB::table('tbladmins as a')
    //    ->orderBy('a.firstname', 'ASC')
    //    ->get(['a.id', 'a.firstname', 'a.lastname']);
    //     return ['data' => $results];
    }
    public function post()
    {
      
    }
}
