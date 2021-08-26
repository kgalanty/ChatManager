<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class Request extends Model
{
    public $timestamps = false;
    protected $table = 'tblcancelrequests';
    //protected $fillable = ['id', 'statsjson', 'created_at'];
    public function service()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Service', 'relid', 'id');
    }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}