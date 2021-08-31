<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class Threads extends Model
{
    public $timestamps = false;
    protected $table = 'chat_threads';
    protected $fillable = ['chatid', 'threadid', 'users', 'domain','agent', 'date'];
    public function service()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Service', 'relid', 'id');
    }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}