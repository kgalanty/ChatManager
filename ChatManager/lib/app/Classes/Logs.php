<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Models\TagHistory;
use WHMCS\Database\Capsule as DB;
use WHMCS\Module\Addon\ChatManager\app\Models\Logs as LogsModel;
use WHMCS\Module\Addon\ChatManager\app\Models\ReviewThread as ReviewThreadModel;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads as ThreadsModel;

class Logs
{
    private static function log($itemid, $itemclass, $doer, $desc = '')
    {
        LogsModel::create(
            [
                'itemid' => $itemid,
                'itemclass' => $itemclass,
                'doer' => $doer,
                'desc' => $desc,
                'created_at' => gmdate('Y-m-d H:i:s')
            ]
        );
    }
    public static function SendToReview($itemid, $doer)
    {
        self::log($itemid, 'Thread', $doer, 'Sent to review');
    }
    public static function MarkAsReviewed($itemid, $doer, $reviewitem)
    {
        $reviewitem = ReviewThreadModel::with('doer')->id($reviewitem)->first();
        $admin = Admin::find($doer);
        $desc = $admin->firstname . ' ' . $admin->lastname . ' has reviewed a comment from ' . $reviewitem->doer->firstname . ' ' . $reviewitem->doer->lastname . ' (' . $reviewitem->comment . ')';
        self::log($itemid, 'Thread', $doer, $desc);
    }
    public static function ProposeNewTag($itemid, $doer, $tag)
    {
        //$reviewitem = ReviewThreadModel::with('doer')->id($reviewitem)->first();
        $admin = Admin::find($doer);
        $thread = ThreadsModel::find($itemid)->chatid;
        $desc = $admin->firstname . ' ' . $admin->lastname . ' propsed to add new tag ' . $tag . ' to chat ' . $thread;
        self::log($itemid, 'Tag', $doer, $desc);
    }
    public static function ProposeDelTag($itemid, $doer, $tag)
    {
        $admin = Admin::find($doer);
        $thread = ThreadsModel::find($itemid)->chatid;
        $desc = $admin->firstname . ' ' . $admin->lastname . ' proposed removal of tag ' . $tag . ' in chat ' . $thread;
        self::log($itemid, 'Tag', $doer, $desc);
    }
    public static function AddTag($itemid, $doer, $tag)
    {
        $admin = Admin::find($doer);
        $thread = ThreadsModel::find($itemid)->chatid;
        $desc = $admin->firstname . ' ' . $admin->lastname . ' approved new tag ' . $tag . ' to chat ' . $thread;
        self::log($itemid, 'Tag', $doer, $desc);
    }
    public static function DelTag($itemid, $doer, $tag)
    {
        $admin = Admin::find($doer);
        $thread = ThreadsModel::find($itemid)->chatid;
        $desc = $admin->firstname . ' ' . $admin->lastname . ' deleted tag ' . $tag . ' in chat ' . $thread;
        self::log($itemid, 'Tag', $doer, $desc);
    }
    public static function updateThread($itemid, $doer, $update, $threaddata)
    {

        foreach ($update as $k => $updateitem) {
            if (
                ($k == 'name' && $updateitem != $threaddata->customer->name && $threaddata->name == null) ||
                ($k == 'email' && $updateitem != $threaddata->customer->email && $threaddata->email == null)
            ) {
                $oldvalue = $threaddata->customer->$k;
            } else {
                $oldvalue = $threaddata->$k;
            }

            if ($updateitem != $threaddata->$k) $logChanges[] = $oldvalue ?? '""' . ' -> ' . $updateitem;
        }
        if (count($logChanges) > 0) {
            $admin = Admin::find($doer);
            $desc = $admin->firstname . ' ' . $admin->lastname . ' has updated chat ' . $threaddata->threadid . ': ' . implode(',', $logChanges);
            self::log($itemid, 'Thread', $doer, $desc);
        }
    }

    public static function FollowUp($threadid, $doer)
    {
        $admin = Admin::find($doer);
        $desc = $admin->firstname . ' ' . $admin->lastname . ' has marked thread #' . $threadid . ' as followed up.';
        self::log($threadid, 'Thread', $doer, $desc);
    }
}
