<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;

class ChatTable extends API
{
    public function get()
    {
        if ($_GET['pending'] == 1 && AuthControl::isAdmin()) {
            $dateTo = $_GET['dateto'] != '' ? $_GET['dateto'] : gmdate('Y-m-d\TH:i:s.000000\Z');
            $result = Threads::with(['tags', 'customer', 'pendingReviews'])
            ->whereHas('pendingReviews',function($q)
            {
                $q->where('pending', '=', '1');
            })
            ->orderBy('id', 'DESC');
            if ($_GET['datefrom']) {
                $result->whereBetween('date', [$_GET['datefrom'], $dateTo]);
            }
             $total = $result->count();
            //$result = DB::select(DB::raw('select *, (select count(*) from `chat_reviewthreads` where `chat_reviewthreads`.`threadid` = `chat_threads`.`id` and `pending` = 1) as `pending_reviews_count` from `chat_threads` having pending_reviews_count > 0 order by `id` desc'));
           // $total = count($result);
            $result = $result->get();
            return ['data' => $result, 'total' => $total];
        }
        $perpage = $_GET['perpage'] ? $_GET['perpage'] : 10;
        $page = $_GET['page'] == 1 ? 0 : ($_GET['page'] - 1) * $_GET['perpage'];
        $dateTo = $_GET['dateto'] != '' ? $_GET['dateto'] : gmdate('Y-m-d\TH:i:s.000000\Z');
        $result = Threads::withCount(['pendingReviews' => function ($q) {
            $q->where('pending', '0');
        }])->has('pendingReviews', '0')
        ->with(['tags', 'customer'])
        ->orderBy('id', 'DESC');
        if ($_GET['datefrom']) {
            $result->whereBetween('date', [$_GET['datefrom'], $dateTo]);
        }
        $total = $result->count();
        $result = $result->skip($page)->take($perpage)->get();
        return ['data' => $result, 'total' => $total];
        //     $results = DB::table('tbladmins as a')
        //    ->orderBy('a.firstname', 'ASC')
        //    ->get(['a.id', 'a.firstname', 'a.lastname']);
        //     return ['data' => $results];
    }
    public function post()
    {
    }
}
