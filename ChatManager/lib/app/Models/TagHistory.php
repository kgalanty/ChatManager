<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;

class TagHistory extends Model
{
    public $timestamps = false;
    protected $table = DBTables::TagHistory;
    protected $fillable = ['thread_id', 'tag', 'doer', 'action', 'created_at'];
    public function doer()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Admin', 'doer', 'id');
    }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}