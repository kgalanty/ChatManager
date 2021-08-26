<?php

namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class CustomOffer extends Model
{
    public $timestamps = false;
    protected $table = 'kg_customoffers';
    //protected $fillable = ['name','ip'];
    protected $visible = ['id','offer'];
    
}