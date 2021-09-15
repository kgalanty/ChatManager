<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class Tags extends Model
{
    public $timestamps = false;
    protected $table = 'chat_tags';
    protected $fillable = ['t_id', 'tag', 'approved'];
    // public function service()
    // {
    //     return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Service', 'relid', 'id');
    // }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}