<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads as ThreadsModel;
class Threads extends API
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
        $order = (int)trim($this->input['order']);
        $itemid = (int)$this->input['id'];
        $notes = trim($this->input['notes']);
        $customoffer = trim($this->input['customoffer']);
        if($itemid)
        {
            $thread = ThreadsModel::where('id', $itemid);
        }
        else
        {
            return 'No thread id was given.';
        }
        $update = [];
        if($name) {             $update['name'] = $name;     }
        if($email) {            $update['email'] = $email;     }
        if($domain) {           $update['domain'] = $domain;     }
        if($order) {            $update['orderid'] = $order;     }
        if($notes) {            $update['notes'] = $notes;     }
        if($customoffer) {      $update['customoffer'] = $customoffer;     }
        // order: this.selectedOrder,
        // notes: this.notes,
        // customoffer: this.customoffer,
        if(count($update) > 0)
        {
            $thread->update($update);
            return 'success';
        }
        return 'Nothing has been changed';
    }
}
