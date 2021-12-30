<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;
use Illuminate\Database\Eloquent\Model;
class Manualpoints extends Model
{
    public $timestamps = false;
    protected $table = DBTables::ManualPoints;
    protected $visible = ['id','userid', 'author', 'agent','points', 'comment', 'date', 'created_at'];
    protected $fillable = ['userid', 'author', 'points', 'comment', 'date', 'created_at'];
    public function author()
    {
        return $this->belongsTo(\WHMCS\Module\Addon\ChatManager\app\Models\Admin::class, 'author', 'id');
    }
    public function agent()
    {
        return $this->belongsTo(\WHMCS\Module\Addon\ChatManager\app\Models\Admin::class, 'userid', 'id');
    }
}