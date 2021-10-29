<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    public $timestamps = false;
    protected $table = 'tblorders';
    //protected $visible = ['id','userid', 'orderid', 'packageid', 'server', 'regdate', 'domain'];
    public function invoice()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Invoice', 'invoiceid', 'id');
    }

}