<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\AdminGroupsConsts;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;

class ChatTable extends API
{
    public function get()
    {
        if ($_GET['pending'] == 1) {
            if (AuthControl::isAdmin()) {
                $dateTo = $_GET['dateto'] != '' ? $_GET['dateto'] : gmdate('Y-m-d\TH:i:s.000000\Z');
                $result = Threads::with(['tags', 'customer', 'pendingReviews', 'followup', 'revieworder', 'agentdata'])
                    ->whereHas('pendingReviews', function ($q) {
                        $q->where('pending', '=', '1');
                    })
                    ->orWhereHas('tags', function ($query) {
                        $query->where('approved', '0')->orWhere('proposed_deletion', '1');
                    })
                    ->orHas('revieworder', '>', '0')
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
            return ['data' => [], 'total' => 0];
        }
        $perpage = $_GET['perpage'] ? $_GET['perpage'] : 10;
        $page = $_GET['page'] == 1 ? 0 : ($_GET['page'] - 1) * $_GET['perpage'];
        $dateTo = $_GET['dateto'] != '' ? $_GET['dateto'] : gmdate('Y-m-d\TH:i:s.000000\Z');
        //$myemail = Admin::where('id', $_SESSION['adminid'])->value('email');
        $tags = $_GET['tags'];
        $operator = $_GET['operator'] != '' ? intval(trim($_GET['operator'])) : '';
        //$myemail = 'emiliya.sergieva@tmdhosting.com';

        $result = Threads::with(['followup', 'order.invoice', 'agentdata'])->withCount(['pendingReviews' => function ($q) {
            $q->where('pending', '0');
        }])
            ->with(['tags', 'customer']);
        if ($_GET['datefrom']) {
            $result->whereBetween('date', [$_GET['datefrom'], $dateTo]);
        }
        if ($operator) {
            $result->whereHas('agentdata', function ($query) use ($operator) {
                $query->where('id', $operator);
            });
        }
        if ($tags) {
            $tagsExploded = explode(',', $tags);
            foreach ($tagsExploded as $tag) {
                $result->whereHas('tags', function ($query) use ($tag) {
                    //$query->whereIn('tag', explode(',', $tags));

                    $query->where('tag', $tag);
                });
            }
        }
        if ($_GET['q']) {
            $q = trim($_GET['q']);
            $result->whereHas('customer', function ($query) use ($q) {
                $query->where('email', 'LIKE', '%' . $q . '%')
                ->orWhere('geolocation', 'LIKE', '%country_code":"' . $q . '%');
            })
                ->orWhere('threadid', 'LIKE', '%' . $q . '%')
                ->orWhere('email', 'LIKE', '%' . $q . '%')
                ->orWhere('domain', 'LIKE', '%' . $q . '%')
                ->orWhere('chatid', 'LIKE', '%' . $q . '%')
                ->orWhere('orderid', 'LIKE', '%' . $q . '%');
               
        }
        if (AuthControl::isAgent()) {
            $result->where('agent',  $_SESSION['adminid']);
            if(isset(AdminGroupsConsts::TAGSAGENTMAP[$_SESSION['adminid']]))
            {
                $result->orWhereHas('tags', function ($query) {
                    $query->where('tag', AdminGroupsConsts::TAGSAGENTMAP[$_SESSION['adminid']]);
                });
            }
        }
        if (AuthControl::isAdmin()) {
            $result->has('pendingReviews', '0');
        }
        $total = $result->count();
        $result = $result->skip($page)->take($perpage)->orderBy('date', 'DESC')
            ->get();

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
