<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class Customers extends Model
{
    public $timestamps = false;
    protected $table = 'chat_customers';
    protected $fillable = ['client_id', 'ip', 'user_agent', 'geolocation'];

    // public function scopeID($query, $serverid)
    // {
    //     return $query->where('id', $serverid);
    // }
}