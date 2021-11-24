<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;

class StaffOnline extends Model
{
    public $timestamps = false;
    protected $table = DBTables::StaffOnline;
    protected $fillable = ['adminid', 'date'];

    public function agent()
    {
        return $this->belongsTo(Admin::class, 'adminid', 'id')->select('id', 'firstname', 'lastname');
    }
}
