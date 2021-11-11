<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;

class ReviewDuplicatedOrder extends Model
{
    public $timestamps = false;
    protected $table = DBTables::ReviewDuplicatedOrders;
    protected $fillable = ['threadid', 'doer', 'created_at'];
    public function doer()
    {
        return $this->belongsTo(Admin::class, 'doer', 'id');
    }
    public function thread()
    {
        return $this->belongsTo(Threads::class, 'threadid', 'id');
    }
    public function scopeThread($query, $threadid)
    {
        return $query->where('threadid', $threadid);
    }
    public function scopeBy($query, $doer)
    {
        return $query->where('doer', $doer);
    }
    public function scopeId($query, $id)
    {
        return $query->where('id', $id);
    }
}