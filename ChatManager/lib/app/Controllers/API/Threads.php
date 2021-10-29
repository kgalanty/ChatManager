<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\APIProtected;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;
use WHMCS\Module\Addon\ChatManager\app\Classes\Logs;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads as ThreadsModel;
use WHMCS\Module\Addon\ChatManager\app\Models\ReviewOrder;

class Threads extends APIProtected
{
    public function get()
    {
        //     $query= $_GET['q'];

        //     if($query)
        //     {
        //         $result = Client::where('firstname', 'LIKE', '%'.$query.'%')
        //             ->orWhere('lastname', 'LIKE', '%'.$query.'%')
        //             ->orWhere('email', 'LIKE', '%'.$query.'%')
        //             ->take(10)
        //             ->get();
        //     }
        //     else
        //     {
        //         $result = Client::take(10)
        //         ->get();
        //     }

        //    // $total = $result->count();
        //     return ['data' => $result];
        //     $results = DB::table('tbladmins as a')
        //    ->orderBy('a.firstname', 'ASC')
        //    ->get(['a.id', 'a.firstname', 'a.lastname']);
        //     return ['data' => $results];
    }
    public function post()
    {
        $name = $this->input['name'];
        $email = trim($this->input['email']);
        $domain = trim($this->input['domain']);
        $order = trim($this->input['order']);
        $itemid = (int)$this->input['id'];
        $notes = trim($this->input['notes']);
        $customoffer = trim($this->input['customoffer']);
        $agent = trim($this->input['agent']);
        if ($itemid) {
            $thread = ThreadsModel::where('id', $itemid);
        } else {
            return 'No thread id was given.';
        }
        $threaddata = $thread->first();
        $update = [];
        if ($name && $name != $threaddata->customer->name ) {
            $update['name'] = $name;
        }
        if ($email && ($email != $threaddata->customer->email || ($threaddata->email && $threaddata->email != $email))) {
            $update['email'] = $email;
        }
        //if ($domain) {
            $update['domain'] = $domain;
       // }
        if ((int)$order != (int)$threaddata->orderid) {
            if(AuthControl::isAgent())
            {
                
                if(ReviewOrder::where('threadid', $itemid)->where('orderid', $order)->count() == 0)
                {
                    ReviewOrder::insert([
                        'orderid' => $order,
                        'threadid' => $itemid,
                        'sender' => $_SESSION['adminid'],
                        'created_at' => gmdate('Y-m-d H:i:s')
                    ]);
                    Logs::submitOrderReview($order, $_SESSION['adminid'], $itemid);
                }
            }
            if(AuthControl::isAdmin())
            {
                $update['orderid'] = (int)$order;
            }
        }
        elseif(strlen($order) == 0)
        {
            $update['orderid'] = null;
        }
        //if ($notes) {
            $update['notes'] = $notes;
       // }
        if ($customoffer) {
            $update['customoffer'] = $customoffer;
        }
        if ($agent && AuthControl::isAdmin()) {
            $update['agent'] = $agent;
        }
       
        Logs::updateThread($itemid, $_SESSION['adminid'], $update, $threaddata);

        // order: this.selectedOrder,
        // notes: this.notes,
        // customoffer: this.customoffer,
        if (count($update) > 0) {
            ThreadsModel::where('id', $itemid)->update($update);
            return 'success';
        }
        return 'Nothing has been changed';
    }
}
