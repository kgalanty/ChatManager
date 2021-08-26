<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class CancelRequest extends Model
{
    public $timestamps = false;
    protected $table = 'kg_cancelrequests';
    protected $fillable = ['date', 'relid', 'cr_id', 'reason','reason_ext', 'livechat_id', 'multiagents', 'type','note_relid'];
    public function service()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Service', 'relid', 'id');
    }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}