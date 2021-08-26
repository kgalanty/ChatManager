<?php
namespace WHMCS\Module\Addon\ChatManager\app\Models;

use Illuminate\Database\Eloquent\Model;
class InvoiceItem extends \WHMCS\Billing\Invoice\Item
{
    public function scopeOnlyAddons($query)
    {
        return $query->where("type", "Addon");
    }

}