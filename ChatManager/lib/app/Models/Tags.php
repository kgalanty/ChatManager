<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;

class Tags extends Model
{
    public $timestamps = false;
    protected $table = DBTables::Tags;
    protected $fillable = ['t_id', 'tag', 'approved'];
    // public function service()
    // {
    //     return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Service', 'relid', 'id');
    // }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
    public function scopeThread($query, $threadid)
    {
        return $query->where('t_id', $threadid);
    }
    public function scopeTag($query, $tag)
    {
        return $query->where('tag', $tag);
    }
}