<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use \Illuminate\Database\Eloquent\Builder;
//use WHMCS\Module\Addon\ChatManager\app\Models\Request as RequestModel;
use WHMCS\Module\Addon\ChatManager\app\Models\CustomOffer as OfferModel;
use WHMCS\Module\Addon\ChatManager\app\Models\CancelRequest;
use WHMCS\Module\Addon\ChatManager\app\Models\Clientgroup;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;
use WHMCS\Module\Addon\ChatManager\app\Models\InvoiceProlong;
use WHMCS\Module\Addon\ChatManager\app\Models\Service;
use WHMCS\Module\Addon\ChatManager\app\Classes\EmailTemplatesConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\SupervisorsConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\EmailHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\Invoices as InvoicesClass;

class CustomOffers extends API
{
    public function get()
    {
        $offers = OfferModel::get();
        
        return ['data' => $offers];
    }
    public function post()
    {
       
    }
}
