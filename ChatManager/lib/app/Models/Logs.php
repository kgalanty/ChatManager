<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;
use Illuminate\Database\Eloquent\Relations\Relation;

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
    public function relateditem()
    {
        Relation::morphMap([
            'Thread' => 'WHMCS\Module\Addon\ChatManager\app\Models\Threads',
            'Tag' => 'WHMCS\Module\Addon\ChatManager\app\Models\Tags',
        ]);
        //return  $this->belongsTo(Threads::class, 'itemid', 'id')->where('itemclass', 'Thread');
        return $this->morphTo(null, 'itemclass', 'itemid');
    }

}