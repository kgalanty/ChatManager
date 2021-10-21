<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class ReviewOrder extends Model
{
    public $timestamps = false;
    protected $table = 'chat_revieworders';
    protected $fillable = ['threadid', 'sender', 'created_at'];
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
        return $query->where('sender', $doer);
    }
    public function scopeId($query, $id)
    {
        return $query->where('id', $id);
    }
}