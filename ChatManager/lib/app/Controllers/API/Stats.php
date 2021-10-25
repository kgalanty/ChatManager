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

class Stats extends API
{
    public function get()
    {
        $dateFrom = $_GET['datefrom'] != '' ? $_GET['datefrom'] : gmdate('Y-m-' . (date('j') < 16 ? 1 : 16) . '\T00:00:00.000000\Z');
        $dateTo = $_GET['dateto'] != '' ? $_GET['dateto'] : gmdate('Y-m-' . (date('j') < 16 ? 15 : 't') . '\T23:59:59.000000\Z');
        $threads = DB::table('chat_threads as t')
            ->join('chat_tags as tg', 'tg.t_id', '=', 't.id')
            ->join('tbladmins as a', 'a.email','=', 't.agent')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->where('tg.approved', 1);
            if (AuthControl::isAgent()) {
                $threads = $threads->where('a.id', $_SESSION['adminid']);
            }
            elseif($_GET['op'] != '')
            {
                $threads = $threads->where('a.id', intval(trim($_GET['op'])));
            }
            $threads = $threads->groupBy('t.agent')
            ->groupBy('tg.tag')
            ->selectRaw('t.agent, a.firstname, a.lastname, a.id as adminid, tg.tag, count(t.id) as count')
            ->get();
          //  echo('<pre>');var_dump($threads);die;
        $r = [];
        $tags = [
            'canoffer' => 0,
            'cannotoffer' => 0,
            'totalsales' => 0,
            'directsale' => 0,
            'convertedsale' => 0,
            'upsell' => 0,
            'cycle' => 0,
            'vpsds' => 0,
            'wcb' => 0,
            'sales' => 0,
            'stayed' => 0,
            'vpsds' => 0,
            'upgrade' => 0
        ];
        foreach ($threads as $t) {
            $r[$t->agent] = array_merge($tags, $r[$t->agent]);
            $r[$t->agent]['agent'] = $t->agent;
            $r[$t->agent]['agent_name'] = $t->firstname.' '.$t->lastname;
            $r[$t->agent][str_replace(' ','',$t->tag)] = $t->count;
        } 
        $o = [];
        foreach($r as $rr)
        {
            $o[] = $rr;
        }
        return ['data' => $o];
    }
    public function post()
    {
    }
}
