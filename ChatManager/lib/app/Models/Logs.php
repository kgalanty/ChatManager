<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;

class Logs extends Model
{
    public $timestamps = false;
    protected $table = DBTables::Logs;
    protected $fillable = ['itemid', 'itemclass', 'doer', 'desc', 'created_at'];
    // public function service()
    // {
    //     return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Service', 'relid', 'id');
    // }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
    public function doer()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Admin', 'doer', 'id');
    }
    public function tag()
    {
        return $this->hasOne('\WHMCS\Module\Addon\ChatManager\app\Models\Tags', 't_id', 'itemid');
    }
}