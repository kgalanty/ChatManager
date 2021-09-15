<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Models\TagHistory;
use WHMCS\Database\Capsule as DB;

class TagsLog
{
    public static function Add($threadid, $tag)
    {
       return self::log($threadid, $tag, 'Add');
    }
    private static function log($thread_id, $tag, $action)
    {
        TagHistory::create(
            [
                'thread_id' => $thread_id,
                'tag' => $tag,
                'doer' => $_SESSION['adminid'],
                'action' => $action,
                'created_at' => gmdate('Y-m-d H:i:s')
            ]
        );
    }
    public static function ProposeDeletion($threadid, $tag)
    {
        return self::log($threadid, $tag, 'Propose Deletion');
    }
    public static function Delete($threadid, $tag)
    {
        return self::log($threadid, $tag, 'Delete');
    }
    public static function Approve($threadid, $tag)
    {
        return self::log($threadid, $tag, 'Approve');
    }
}