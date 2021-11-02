<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes\Cron;

use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use WHMCS\Module\Addon\ChatManager\app\Models\Client;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\Logs;
use WHMCS\Module\Addon\ChatManager\app\Classes\TagsLog;
use WHMCS\Module\Addon\ChatManager\app\Models\Tags;
use WHMCS\Module\Addon\ChatManager\app\Classes\TagsHelper;
use WHMCS\Module\Addon\ChatManager\app\DBTables\DBTables;

class OrderCron
{
    public $timezone;
    public function __construct($timezone)
    {
        if ($timezone) {
            $this->timezone = $timezone;
        }
    }
    private function getThreads()
    {
        $threads = Threads::with(['tags', 'customer'])
            ->whereNull('orderid')
            ->where('created_at', '>', date('Y-m-d h:i:s', strtotime("-45 days")))
            ->get();
        return $threads;
    }
    private function processThreads($threads = [])
    {
        foreach ($threads as $thread) {
            $this->singleThreadHandler($thread);
        }
    }
    private function singleThreadHandler($thread)
    {
        $potentiallyCompletedOrder = DB::table('chat_completedorders as co')
            ->join('tblorders as o', 'o.id', '=', 'co.ordernumber')
            ->join('tblinvoices as i', 'i.id', '=', 'o.invoiceid')
            ->join('tblinvoiceitems as ii', function ($join) {
                $join->on('ii.invoiceid', '=', 'i.id');
                $join->where('ii.type', '=', 'Hosting');
            })
            ->join('tblhosting as h', 'h.id', '=', 'ii.relid')
            ->leftJoin('chat_threads as t', 't.orderid', '=', 'o.id')
            ->where('i.status', 'Paid')
            ->where('lcchatid', $thread->chatid)
            ->first(['h.domain', 'co.ordernumber as orderid']);
        if ($potentiallyCompletedOrder) {
            $thread->orderid = $potentiallyCompletedOrder->orderid;
            $thread->domain = $potentiallyCompletedOrder->domain;
            Threads::where('id', $thread->id)
                ->update(['domain' => $potentiallyCompletedOrder->domain, 'orderid' => $potentiallyCompletedOrder->orderid]);
            if (Tags::thread($thread->id)->tag('wcb')->count() > 0) {
                TagsHelper::addTag('convertedsale', $thread->id, true, 1);
                TagsLog::AddedByCron($thread->id, 'convertedsale');
            }

            Logs::MatchedOrderByCron($thread);
            
            return;
        }
        $email = $thread->email ? $thread->email : $thread->customer->email;
        //  if($email != 'domain@pushpatechnologies.com') return;
        if ($email) {
            $client = Client::where('email', $email)->first(['id']);

            if ($client) {
                //Threads::where('id', $thread->id)->update()
                $order = DB::table('tblorders as o')
                    ->join('tblinvoices as i', 'i.id', '=', 'o.invoiceid')
                    ->join('tblinvoiceitems as ii', function ($join) {
                        $join->on('ii.invoiceid', '=', 'i.id');
                        $join->where('ii.type', '=', 'Hosting');
                    })
                    ->join('tblhosting as h', 'h.id', '=', 'ii.relid')
                    ->leftJoin(DBTables::Threads.' as t', 't.orderid', '=', 'o.id')
                    ->where('o.userid', $client->id)
                    ->whereBetween('o.date', [date('Y-m-d h:i:s', strtotime($thread->date . " -1 days")), date('Y-m-d h:i:s')])
                    ->where('o.status', 'Active')
                    ->where('i.status', 'Paid')
                    ->whereNull('t.orderid')
                    ->first(['h.domain', 'o.id as orderid']);
                // var_dump($order);die;
                if ($order) {
                    if ($order->domain) {
                        Threads::where('id', $thread->id)->update(['domain' => $order->domain, 'orderid' => $order->orderid]);
                        if (Tags::thread($thread->id)->tag('wcb')->count() > 0) {
                            TagsHelper::addTag('convertedsale', $thread->id, true, 1);
                            TagsLog::AddedByCron($thread->id, 'convertedsale');
                        }
                    }
                }
            }
        }
    }
    public function run()
    {
        $threads = $this->getThreads();
        if (!$threads) return;
        $this->processThreads($threads);

        $unpaidThreads = $this->getUnpaidThreads();
    }
    private function getUnpaidThreads()
    {
        
    }
}
