<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes\ThreadEditFields;

class ClientName implements IThreadEditField
{
    public static function handle($field, $threaddata)
    {
      
        if($field == null) return;

        if($field != $threaddata->name)
        {
            $name = strlen($field) == 0 ? '""' : $field;
            return 'Client: '. $name.'=>'.$field;
        }

    }
}