<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes\ThreadEditFields;

class Domain implements IThreadEditField
{
    public static function handle($field, $threaddata)
    {
        if($field == null) return;

        if($field != $threaddata->domain)
        {
            $olddomain = strlen($field) == 0 ? '""' : $field;
            return 'Domain: '. $olddomain.'=>'.$field;
        }

    }
}