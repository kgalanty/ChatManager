<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\Models\Tags;
use WHMCS\Module\Addon\ChatManager\app\Models\Customers;
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
    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}