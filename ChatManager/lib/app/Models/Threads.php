<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\Models\Tags;
use WHMCS\Module\Addon\ChatManager\app\Models\Customers;
use WHMCS\Module\Addon\ChatManager\app\Models\ReviewThread;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;
use WHMCS\Module\Addon\ChatManager\app\Models\Followup as FollowupModel;
class Threads extends Model
{
    public $timestamps = false;
    protected $table = 'chat_threads';
    protected $fillable = ['chatid', 'threadid', 'users', 'domain','agent', 'date', 'created_at'];
    public function tags()
    {
        return $this->hasMany(Tags::class, 't_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'users', 'client_id');
    }
    public function pendingReviews()
    {
        return $this->hasMany(ReviewThread::class, 'threadid', 'id')->where('pending', '1');
    }
    public function scopeAgent()
    {
        return $this->belongsTo(Admin::class, 'agent', 'email');
    }
    public function followup()
    {
        return $this->hasMany(FollowupModel::class, 'threadid', 'id');
    }
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}