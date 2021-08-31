<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;


class DateTimeHelper
{
    //moment().format('Z')
   public static function subDate(string $timezone = '+03:00', \DateTimeInterval $interval = null) : \DateTime
   {
       if($interval === null)
       {
           $interval = new \DateInterval('PT1H');
       }
        $now = new \DateTime( $timezone );
        return $now->sub($interval);
   }

}