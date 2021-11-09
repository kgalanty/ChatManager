<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\APIProtected;
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
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;
use WHMCS\Module\Addon\ChatManager\app\Classes\StatsHelper;
class Stats extends API
{
    public function get()
    {
        $dateFrom = $_GET['datefrom'] != '' ? $_GET['datefrom'] : gmdate('Y-m-' . (date('j') < 16 ? 1 : 16) . '\T00:00:00.000000\Z');
        $dateTo = $_GET['dateto'] != '' ? $_GET['dateto'] : gmdate('Y-m-' . (date('j') < 16 ? 15 : 't') . '\T23:59:59.000000\Z');
        if($_GET['a'] == 'StatsDetails')
        {
            $data['data'] = StatsHelper::getTagsFrequency(['datefrom' => $dateFrom, 'dateto' => $dateTo, 'op' => $_GET['op']]);
            return $data;  
        }
        // if($_GET['a'] == 'StatsDetails')
        // {
        //     return StatsHelper::Details(['datefrom' => $dateFrom, 'dateto' => $dateTo, 'op' => $_GET['op']]);
        // }
        $threads = StatsHelper::getStats( ['datefrom' => $dateFrom, 'dateto' => $dateTo, 'op' => $_GET['op']]);
        $cm_stayed_requests = StatsHelper::getPointsFromCancellations(['datefrom' => $dateFrom, 'dateto' => $dateTo, 'op' => $_GET['op']]);
        //calculate how many points per agent have to be substracted, as 'upgrade' tag should count as 1 in one thread 
        // even among other pointgiving tags.
        //This is returned and substracted on frontend. Raw query for speed gain
        $threads_upgrade_points = StatsHelper::getDecrementPoints( ['datefrom' => $dateFrom, 'dateto' => $dateTo, 'op' => $_GET['op']]);
        $o = StatsHelper::CreateResult($threads, $threads_upgrade_points, $cm_stayed_requests);

        
        return ['data' => $o];
    }
    public function post()
    {
    }
}
