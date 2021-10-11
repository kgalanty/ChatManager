<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes\Cron;

use WHMCS\Module\Addon\ChatManager\app\Classes\AdminGroupsConsts;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use WHMCS\Module\Addon\ChatManager\app\Models\Client;
use WHMCS\Database\Capsule as DB;
class OrderCron
{
    public $timezone;
    public function __construct($timezone)
    {
        if($timezone)
        {
            $this->timezone = $timezone;
        }
    }
    private function getThreads()
    {
        $threads = Threads::with('customer')->whereNull('orderid')->where('created_at', '>', date('Y-m-d h:i:s', strtotime("-45 days")))->get();
        return $threads;
    }
    private function processThreads($threads = [])
    {
        foreach($threads as $thread)
        {
            $this->singleThreadHandler($thread);
        }
    }
    private function singleThreadHandler($thread)
    {
        $email = $thread->email ? $thread->email : $thread->customer->email;
      //  if($email != 'domain@pushpatechnologies.com') return;
        if($email)
        {
           $client = Client::where('email', $email)->first(['id']);
       
           if($client)
           {
               //Threads::where('id', $thread->id)->update()
               $order = DB::table('tblorders as o')
               ->join('tblinvoices as i', 'i.id', '=', 'o.invoiceid')
               ->join('tblinvoiceitems as ii', function($join)
               {    
                   $join->on('ii.invoiceid', '=', 'i.id');
                   $join->where('ii.type', '=', 'Hosting');
               })
               ->join('tblhosting as h', 'h.id', '=', 'ii.relid')
               ->leftJoin('chat_threads as t', 't.orderid', '=', 'o.id')
               ->where('o.userid', $client->id)
               ->whereBetween('o.date', [date('Y-m-d h:i:s', strtotime($thread->date." -1 days")), date('Y-m-d h:i:s')])
               ->where('o.status', 'Active')
               ->where('i.status', 'Paid')
               ->whereNull('t.orderid')
               ->first(['h.domain', 'o.id as orderid']);
              // var_dump($order);die;
               if($order)
               {
                    if($order->domain)
                    {
                        Threads::where('id', $thread->id)->update(['domain'=>$order->domain, 'orderid' => $order->orderid]);
                    }
               }
           }
        }
    }
    public function run()
    {
        $threads = $this->getThreads();
        if(!$threads) return;
        $this->processThreads($threads);
    }
    
}
