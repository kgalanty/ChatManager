<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    public $timestamps = false;
    protected $table = 'tblproducts';
    //protected $visible = ['id','userid', 'orderid', 'packageid', 'server', 'regdate', 'domain'];
    public function group()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\ProductGroup', 'gid', 'id');
    }
}