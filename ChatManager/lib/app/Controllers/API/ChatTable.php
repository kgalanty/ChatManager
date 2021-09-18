<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;

class ChatTable extends API
{
    public function get()
    {
        if ($_GET['pending'] == 1) {
            $dateTo = $_GET['dateto'] != '' ? $_GET['dateto'] : gmdate('Y-m-d\TH:i:s.000000\Z');
            $result = Threads::withCount(['pendingReviews' => function ($q) {
                $q->where('pending', '1');
            }])->with(['tags', 'customer'])->has('pendingReviews', '>', '0')->orderBy('id', 'DESC');
            if ($_GET['datefrom']) {
                $result->whereBetween('date', [$_GET['datefrom'], $dateTo]);
            }
            $total = $result->count();
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
