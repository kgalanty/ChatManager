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
use WHMCS\Module\Addon\ChatManager\app\Models\Note;

class Stats extends APIProtected
{
    public function get()
    {
        $date1 = $_GET['d1'] ? date('Y-m-d 00:00:00', strtotime($_GET['d1'])) : date('Y-m-01 00:00:00');
        $date2 = $_GET['d2'] ? date('Y-m-d 23:59:59', strtotime($_GET['d2'])) : date('Y-m-t 23:59:59');

        $total = CancelRequest::whereHas('service.product', function(\Illuminate\Database\Eloquent\Builder $query)
        {
            $query->whereNotIn('gid', GidsConsts::EXTRASERVICESGIDS);
        })
        ->wherein('action', ['stayed', 'left'])
        ->whereBetween('date', [$date1, $date2])
        ->count();
       $stayed = CancelRequest::whereHas('service.product', function(\Illuminate\Database\Eloquent\Builder $query)
       {
           $query->whereNotIn('gid', GidsConsts::EXTRASERVICESGIDS);
       })
       ->where('action', 'stayed')
       ->whereBetween('date', [$date1, $date2])
       ->count();
       $left = CancelRequest::whereHas('service.product', function(\Illuminate\Database\Eloquent\Builder $query)
       {
           $query->whereNotIn('gid', GidsConsts::EXTRASERVICESGIDS);
       })
       ->whereBetween('date', [$date1, $date2])
       ->where('action', 'left')->count();
       $deleted = CancelRequest::whereHas('service.product', function(\Illuminate\Database\Eloquent\Builder $query)
       {
           $query->whereNotIn('gid', GidsConsts::EXTRASERVICESGIDS);
       })
       ->where('action', 'delete')
       ->whereBetween('date', [$date1, $date2])
       ->count();
       $confirmed = DB::select( DB::raw("select count(*) as count FROM (select count(e.relid) from (select c.relid, c.action from kg_cancelrequests c

       where c.relid in (select relid
       from `kg_cancelrequests`
       
       group by relid
       having count(*) > 2 and `c`.`action` = 'delete'
       )
       UNION
       select d.relid, d.action from kg_cancelrequests d
       where d.relid in (select relid
       from `kg_cancelrequests`
       
       group by relid
       having count(*) > 2 and `d`.`action` = 'left'
       and d.date between '$date1' AND '$date2'
       )
       ) e
       group by e.relid 
       having count(e.relid) > 1) f" ))[0];
       $retention = (int)round($stayed/($stayed+$left)*100);
        return ['TotalC' => $total, 'Stayed' => $stayed, 'Left' => $left, 'Retention' => $retention.'%', 'Deleted' => $deleted, 'Confirmed'=>$confirmed->count];
    }
    public function post()
    {
      
    }
}
