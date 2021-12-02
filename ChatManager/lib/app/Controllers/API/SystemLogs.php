<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Module\Addon\ChatManager\app\Models\Logs as LogsModel;
use WHMCS\Module\Addon\ChatManager\app\Classes\AuthControl;

class SystemLogs extends API
{
    public function get()
    {
        if (AuthControl::isAdmin()) {
            $perpage = $_GET['perpage'] ? $_GET['perpage'] : 25;
            $page = (!$_GET['page'] || $_GET['page'] == 1) ? 0 : ((int)$_GET['page'] - 1) * $perpage;
            $logs = LogsModel::with(['doer', 'relateditem'])
                ->orderBy('id', 'DESC');
            $total = $logs->count();
            $result = $logs->skip($page)
                ->take($perpage)
                ->orderBy('id', 'DESC')
                ->get();
            return ['result' => 'success', 'data' => $result, 'total' => $total];
        }
    }
    public function post()
    {
    }
}
