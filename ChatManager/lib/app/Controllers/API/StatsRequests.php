<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\APIProtected;
use WHMCS\Database\Capsule as DB;
//use WHMCS\Module\Addon\ChatManager\app\Models\Request as RequestModel;
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
use WHMCS\Module\Addon\ChatManager\app\Classes\StatsRoleHelper;
use WHMCS\Module\Addon\ChatManager\app\Models\Note;

class StatsRequests extends APIProtected
{
    public function get()
    {
        if($_GET['action']==='peragent')
        {
            if($_GET['agentid'])
            {
                //$results = $results->where('c.agent', $_GET['agentid']);
                $results = DB::table('kg_cancelrequests as cr')
                ->join('tblhosting as h', 'h.id', '=', 'cr.relid')
                ->join('tblproducts as p', 'p.id', '=', 'h.packageid')
                ->whereNotIn('p.gid', GidsConsts::EXTRASERVICESGIDS)
                ->whereRaw('cr.date > now() - INTERVAL 12 month')
                ->groupBy(DB::raw('year(cr.date), month(cr.date), cr.action, cr.agent'))
                ->having('cr.agent', '=', $_GET['agentid'])
                ->havingRaw('cr.action IS NOT NULL')
                ->selectRaw('year(cr.date) as y,month(cr.date) as m, count(cr.action) as c, cr.action')
                ->orderBy('y', 'DESC')
                ->orderBy('m', 'DESC')
                ->get();
                $return = [];
                foreach($results as $r)
                {
                    $return[$r->y][$r->m][$r->action] = $r->c;
                }
                $return2=[];
                foreach($return as $year_key=>$year)
                {
                    foreach($year as $month=>$v)
                    {
                        
                        $return2[] = array_merge(['year' => $year_key, 'month' => $month, 'total'=>array_sum($v)], $v);
                    }
                }
                return ['results' =>$return2];
                
            }
            //unset($_SESSION['ChatManager_stats']);
            $fields = [
                'a.firstname', 
                'a.lastname',
                'a.id as aid',
                'count(*) as count',
                 'c.agent',
                 'c.action'
            ];
            $date1 = $_GET['date1'] ? date('Y-m-d 00:00:00', strtotime($_GET['date1'])) : date('Y-m-01 00:00:00');
            $date2 = $_GET['date2'] ? date('Y-m-d 23:59:59', strtotime($_GET['date2'])) : date('Y-m-t 23:59:59');
          
            $results = DB::table('kg_cancelrequests as c')
            ->join('tbladmins as a', 'a.id', '=', 'c.agent')
            ->join('tblhosting as h', 'h.id', '=', 'c.relid')
            ->join('tblproducts as p', 'p.id', '=', 'h.packageid')
            ->select(DB::raw(implode(',',$fields)))
            ->whereNotNull('c.action')
            ->whereNotIn('p.gid', GidsConsts::EXTRASERVICESGIDS)
            ->whereBetween('c.date', [$date1, $date2])
            ->groupBy(['c.agent','c.action']);
            
            // if(!StatsRoleHelper::canEnterSTats())
            // {
            //     $results = $results->where('c.agent', $_SESSION['adminid']);
            //    // var_dump($_SESSION['adminid']);die;
            // }
            // if($_GET['test']=='test123')
            // {
            //     unset($_SESSION['ChatManager_stats']);
            //     var_dump(StatsRoleHelper::canEnterSTats());die;
            // }
            $results = $results->get();
            if($results == [])
            {
                $admin = DB::table('tbladmins')->where('id', $_GET['agentid'])->first();
                $return[0] = ['agentid' => $admin->id, 'delete'=>0, 'stayed' => 0, 'left' => 0, 'total'=>0, 'admin' =>  $admin->firstname.' '.$admin->lastname];
                //$return['admin'] = 
            }
            else {
                $return = [];
                foreach($results as $r)
                {
                    $action = $r->action? $r->action : 'No action';
                    $return[$r->aid][$action] += $r->count;
                    $return[$r->aid]['admin'] = $r->firstname.' '.$r->lastname;
                    $return[$r->aid]['total'] += $r->count;
                    $return[$r->aid]['agentid'] = $r->aid;
                }
                foreach($return as &$r)
                {
                    $r['delete'] = $r['delete'] ? $r['delete'] : 0;
                    $r['stayed'] = $r['stayed'] ? $r['stayed'] : 0;
                    $r['left'] = $r['left'] ? $r['left'] : 0;
                    $r['total'] = $r['total'] ? $r['total'] : 0;
                }
        }
            return ['results' => array_values($return)];

        }
        //unset($_SESSION['ChatManager_stats']);
        // if(!StatsRoleHelper::canEnterSTats()) {
        //     return;
        // }
        $page = $_GET['page'] == 1 ? 0 : ($_GET['page'] - 1) * $_GET['perpage'];
        $vpsdsGids = GidsConsts::VPSGIDS;
        $sharedGids = GidsConsts::SHAREDGIDS;
        $extraServicesGids = GidsConsts::EXTRASERVICESGIDS;
        //return Clients::with(['clientgroup'])->get(); 'service', 'service.client', 
        //var_dump(RequestModel::with('service.client.clientgroup')->get()); die;
        //    $r = [];
        //    $requests = RequestModel::has('service')
        //    ->has('service.client')
        //    ->has('service.client.clientgroup')
        //    ->with('service')
        //    ->with('service.client.clientgroup')
        //    ->limit(10)
        //    ->get();
        $tblhosting = [
            'h.userid',
            'h.orderid',
            'h.packageid',
            'h.server',
            'h.regdate',
            'h.domain',
            // 'h.paymentmethod',
            // 'h.amount',
            'h.billingcycle',
            'h.nextduedate',
            'h.nextinvoicedate',
            'h.termination_date',
            'h.completed_date',
            'h.domainstatus',
            'h.subscriptionid',
        ];
        $tblclients = [
            'c.firstname',
            'c.lastname',
            'c.companyname',
            'c.email',

        ];
        $tbladmins = ['ad.firstname as admin_firstname ', 'ad.lastname as admin_lastname'];
        $tblproductgroups = ['gids.name AS gids_name'];
        $tblproducts = ['p.name AS productname', 'p.gid'];
        $date1 = $_GET['d1'] ? date('Y-m-d 00:00:00', strtotime($_GET['d1'])) : date('Y-m-01 00:00:00');
        $date2 = $_GET['d2'] ? date('Y-m-d 23:59:59', strtotime($_GET['d2'])) : date('Y-m-t 23:59:59');
        $agent = $_GET['agent'] ? (int)$_GET['agent'] : '';
        $requests = DB::table('kg_cancelrequests as cr')
            ->join('tblhosting as h', 'h.id', '=', 'cr.relid')
            ->join('tblproducts as p', 'p.id', '=', 'h.packageid')
            ->join('tblproductgroups as gids', 'gids.id', '=', 'p.gid')
            ->join('tblclients as c', 'c.id', '=', 'h.userid')
            ->leftJoin('kg_customoffers as co', 'co.id', '=', 'cr.customoffer')
            ->leftJoin('tbladmins as ad', 'ad.id', '=', 'cr.agent')
            ->whereBetween('cr.date', [$date1, $date2])
            ;
            if($_GET['agent'] > -1)
            {
                $requests = $requests->where('agent', (int)$_GET['agent']);
            }
            if($_GET['rt'] > -1 && $_GET['r'] > -1)
            {
                if($_GET['rt'] == 'reason')
                {
                    $requests = $requests->where('reason', $_GET['r']);
                }
                elseif($_GET['rt'] == 'reasonext')
                {
                    $requests = $requests->where('reason_ext', $_GET['r']);
                }
            }
            if($_GET['o'] > -1)
            {
                $actions = ['left', 'stayed'];
                $outcome = trim(strtolower($_GET['o']));
                if(in_array($outcome, $actions))
                {
                    $requests = $requests->where('cr.action', $outcome);
                }
            }
            if($_GET['gid'] > -1)
            {
                $requests = $requests->where('p.gid', (int)$_GET['gid'] );
            }
            if($_GET['co'] > -1)
            {
                $requests = $requests->where('cr.customoffer', (int)$_GET['co']);
                
            }
            if($_GET['action'] === 'extra')
            {
                $requests = $requests->whereNotIn('p.gid', $vpsdsGids)->whereNotIn('p.gid', $sharedGids);
            }
            else
            {
                $requests = $requests->whereNotIn('p.gid', $extraServicesGids)->where('cr.action', '<>', 'delete');
            }
            if($_GET['conf']==1)
            {
                $requests = $requests->whereNull('cr.agent_confirmed')->where('cr.multiagents', '1');
            }
            //delete actions are counted but not displayed in the table
        // `d1=${this.StartFromDate}`,
        // `d2=${this.EndToDate}`,

        //$secondQr = clone $requests;
        $total = $requests->count();
        // $requests = $requests
        // ->select(array_merge([
        //     'cr.*'
        // ], $tblhosting, $tblclients, $tblproducts));
        //$secondQr = $secondQr->union($requests)->where('cr.date', '<=', DB::raw('DATE_SUB(NOW(), INTERVAL 3 day)'));

        

        //$common = [];
        //$rqCount = $requests->count();
        //if ($rqCount < $_GET['perpage']) {
            // $common = clone $requests;
            // if ($rqCount == 0) {
            //     $common = $requests->skip($page)->take($_GET['perpage']);
            // }
            // $common = $common->take($_GET['perpage'] - $rqCount)->where('cr.date', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 3 day)'))->orderBy('id', 'DESC')
            //     ->get(array_merge([
            //         'cr.*'
            //     ], $tblhosting, $tblclients, $tblclientgroups, $tblproducts));
        //}
        $requests = $requests->skip($page)->take($_GET['perpage'])->orderBy('cr.date', 'DESC')
            ->get(array_merge([
                'cr.*', 'co.offer'
            ], $tblhosting, $tblclients, $tblproducts, $tblproductgroups, $tbladmins));
           // var_dump($requests);die;
        //    foreach($requests as &$req)
        //    {
    
        //        if(CancelRequest::where('relid', $req->relid)->where('action', 'delete')->count()>1)
        //        {
        //            $req->urgent = 1;
        //        }
        //    }
        return ['total' => $total, 'data' => $requests];
    }
    public function post()
    {
        if(!StatsRoleHelper::canEnterSTats()) {
            return;
        }
        $admin = Admin::find($_SESSION['adminid']);
        $action = strtolower($this->input['data']['type']);
        $hid = intval($this->input['data']['hid']);
        $crid = $this->input['data']['crid'];
        if(!$this->input['data']) return;
        
        switch($_GET['act'])
        {
            case 'UpdateSupervisor':
                if($this->input['data']['cr_id'] && $this->input['data']['agent'])
                {
                    CancelRequest::where('id', $this->input['data']['cr_id'])
                    ->update(['agent'=> $this->input['data']['agent']]);
                    return 'success';
                }

                break;
                case 'ConfirmAgent':
                    if($this->input['data']['cr_id'] && $this->input['data']['agent'])
                    {
                        $agent_confirmation = ['admin' => $_SESSION['adminid'], 'date' => date('Y-m-d H:i:s')];
                        CancelRequest::where('id', $this->input['data']['cr_id'])
                        ->update(['agent_confirmed'=> json_encode($agent_confirmation)]);
                        return 'success';
                    }
                    break;

        }
        return 'Bad action';


    }
}
