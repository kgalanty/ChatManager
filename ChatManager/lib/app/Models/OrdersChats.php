<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;
use \WHMCS\Module\Addon\ChatManager\app\Models\Order;
use \WHMCS\Module\Addon\ChatManager\app\Models\Customers;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use Illuminate\Database\Eloquent\Model;
class OrdersChats extends Model
{
    public $timestamps = false;
    protected $table = DBTables::OrdersChats;
    //protected $visible = ['id','userid', 'orderid', 'packageid', 'server', 'regdate', 'domain'];
    public function order()
    {
        return $this->belongsTo(Order::class, 'ordernumber', 'id');
    }
    public function lccustomer()
    {
        return $this->belongsTo(Customers::class, 'lcvisitorid', 'client_id');
    }
    public function chat()
    {
        return $this->belongsTo(Threads::class, 'lcchatid', 'chatid');
    }

}