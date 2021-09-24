<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Models\Tags as Tag;
use WHMCS\Module\Addon\ChatManager\app\Models\TagHistory;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;

class Orders extends API
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
        if ($this->input['a'] == 'CheckOrderID') {
            $id = (int)$this->input['order'];
            $threadid = (int)$this->input['threadid'];
            $order = Threads::where('orderid', $id)->where('id', '<>', $threadid)->first();
            if($order)
            {
                return 'This Order is already assigned to chat '.$order->threadid;
            }
            else
            {
                return 'success';
            }
        }
    }
}
