<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;
use WHMCS\Module\Addon\ChatManager\app\Models\Tags;
use WHMCS\Module\Addon\ChatManager\app\Models\Customers;
use WHMCS\Module\Addon\ChatManager\app\Models\ReviewThread;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;
use WHMCS\Module\Addon\ChatManager\app\Models\FollowUp as FollowupModel;
use WHMCS\Module\Addon\ChatManager\app\Models\ReviewOrder;
use WHMCS\Module\Addon\ChatManager\app\Models\Order;
use WHMCS\Module\Addon\ChatManager\app\Models\Invoice;
use WHMCS\Module\Addon\ChatManager\app\Models\TagHistory;
class Threads extends Model
{
    public $timestamps = false;
    protected $table = DBTables::Threads;
    protected $fillable = ['chatid', 'threadid', 'users', 'domain','name', 'email', 'orderid', 'invoiceid','agent', 'date', 'notes','created_at'];
    public function tags()
    {
        return $this->hasMany(Tags::class, 't_id', 'id');
    }
    public function tagshistory()
    {
        return $this->hasMany(TagHistory::class, 'thread_id', 'id');
    }
    public function reviewthread()
    {
        return $this->hasMany(ReviewThread::class, 'threadid', 'id');
    }
    public function logs()
    {
        return $this->hasMany(Logs::class, 'itemid', 'id')->where('itemclass', 'Thread');
    }
    public function customer()
    {
        return $this->hasMany(Customers::class, 'client_id', 'users');
    }
    public function pendingReviews()
    {
        return $this->hasMany(ReviewThread::class, 'threadid', 'id')->where('pending', '1');
    }
    public function followup()
    {
        return $this->hasMany(FollowupModel::class, 'threadid', 'id');
    }
    public function revieworder()
    {
        return $this->hasMany(ReviewOrder::class, 'threadid', 'id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'orderid', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'id');
    }
    public function sameorder()
    {
        return $this->hasMany($this, 'orderid', 'orderid');
    }
    public function reviewduplicatedorders()
    {
        return $this->hasOne(ReviewDuplicatedOrder::class, 'threadid', 'id');
    }
    public function agentdata()
    {
        return $this->belongsTo(Admin::class, 'agent', 'id')->select('id', 'firstname', 'lastname');
    }
    public function scopeOrderOf($query, $order)
    {
        return $query->where('orderid', $order);
    }
}