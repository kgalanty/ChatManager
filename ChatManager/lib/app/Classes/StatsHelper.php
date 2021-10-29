<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;
class StatsHelper
{
    public static function getStats($params)
    {
        $threads = DB::table('chat_threads as t')
        ->join('chat_tags as tg', 'tg.t_id', '=', 't.id')
        ->join('tbladmins as a', 'a.email', '=', 't.agent')
        ->whereBetween('date', [$params['datefrom'], $params['dateto']])
        ->where('tg.approved', 1);
    if (AuthControl::isAgent()) {
        $threads = $threads->where('a.id', $_SESSION['adminid']);
    } elseif ($_GET['op'] != '') {
        $threads = $threads->where('a.id', intval(trim($_GET['op'])));
    }
    $threads = $threads->groupBy('t.agent')
        ->groupBy('tg.tag')
        ->selectRaw('t.agent, a.firstname, a.lastname, a.id as adminid, tg.tag, count(t.id) as count')
        ->get();
        return $threads;
    }
    public static function getDecrementPoints($params)
    {
        $q = 'select sum(x.c) as s, agent FROM ( select
            t.agent, count(t.id) as c from `chat_threads` t 
            join `chat_tags` as tg ON tg.t_id = t.id
            join `tbladmins` as ad ON ad.email = t.agent
            where 
            tg.tag in ("upgrade", "cycle", "upsell")
            and exists (select id from chat_tags as tg where tg.tag = "upgrade" and tg.t_id = t.id and tg.approved = 1)
            and date between ? and ?
            ';
        if (AuthControl::isAgent()) {
            $q .= 'and agent = ' . $_SESSION['adminid'];
        } elseif ($_GET['op'] != '') {
            $q .= ' and ad.id = ' . intval(trim($_GET['op']));
        }
        $q .= ' group by t.id, t.agent having c > 1) as x
            group by agent';

        $threads_upgrade_points = collect(DB::select($q, [$params['datefrom'], $params['dateto']]))->keyBy('agent');
        return $threads_upgrade_points;
    }
    public static function CreateResult($threads, $threads_upgrade_points)
    {
        $r = [];
        $tags = [
            'canoffer' => 0,
            'cannotoffer' => 0,
            'totalsales' => 0,
            'directsale' => 0,
            'convertedsale' => 0,
            'upsell' => 0,
            'cycle' => 0,
            'vpsds' => 0,
            'wcb' => 0,
            'sales' => 0,
            'stayed' => 0,
            'vpsds' => 0,
            'upgrade' => 0
        ];
        //rearrange data from query to one unified array as query returns scattered data across rows
        foreach ($threads as $t) {
            $r[$t->agent] = array_merge($tags, $r[$t->agent]);
            $r[$t->agent]['agent'] = $t->agent;
            $r[$t->agent]['agent_name'] = $t->firstname . ' ' . $t->lastname;
            $r[$t->agent][str_replace(' ', '', $t->tag)] = $t->count;
        }
        $o = [];
        //add points to decrement to final array
        foreach ($r as $agent_email => $rr) {

            $rr['decrementpoints'] = $threads_upgrade_points[$agent_email] ? (int)$threads_upgrade_points[$agent_email]->s - 1 : 0;
            $o[] = $rr;
        }
        return $o;
    }
    // private static function log($thread_id, $tag, $action)
    // {
    //     TagHistory::create(
    //         [
    //             'thread_id' => $thread_id,
    //             'tag' => $tag,
    //             'doer' => $_SESSION['adminid'],
    //             'action' => $action,
    //             'created_at' => gmdate('Y-m-d H:i:s')
    //         ]
    //     );
    // }
    // public static function ProposeDeletion($threadid, $tag)
    // {
    //     return self::log($threadid, $tag, 'Propose Deletion');
    // }
    // public static function Delete($threadid, $tag)
    // {
    //     return self::log($threadid, $tag, 'Delete');
    // }
    // public static function Approve($threadid, $tag)
    // {
    //     return self::log($threadid, $tag, 'Approve');
    // }
}