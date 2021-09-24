<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class FollowUp extends Model
{
    public $timestamps = false;
    protected $table = 'chat_followup';
    protected $fillable = ['threadid', 'followupdate', 'doer'];
    public function doer()
    {
        return $this->belongsTo('\WHMCS\Module\Addon\ChatManager\app\Models\Admin', 'sender', 'id');
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