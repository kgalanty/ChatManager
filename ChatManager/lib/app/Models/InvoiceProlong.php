<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class InvoiceProlong extends Model
{
    public $timestamps = false;
    protected $table = 'kg_invoiceprolong';
    protected $fillable = ['invoiceid', 'relid'];

    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}