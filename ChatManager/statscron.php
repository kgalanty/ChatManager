<?php
require_once(__DIR__.'/../../../init.php');
use WHMCS\Module\Addon\ChatManager\app\Models\ServersPools;
use WHMCS\Module\Addon\ChatManager\app\Classes\Stats;
use WHMCS\Module\Addon\ChatManager\app\Models\ServersStats;
use WHMCS\Module\Addon\ChatManager\app\Classes\LogsHelper;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
list($starttime) = hrtime();
$servers = ServersPools::with('server')->get();

foreach($servers as $server)
{
    list($active, $total, $suspended) = Stats::getStats($server->server);
    $stats = ['active' => $active, 'total' => $total, 'suspended' => $suspended];

    ServersStats::updateOrCreate(['id' => $server->server->id], [ 
        'statsjson' => json_encode($stats),
        'created_at' => date('Y-m-d H:i:s')
    ]);
}
list($endtime) = hrtime();
LogsHelper::log('Cron', '0', 'Cron finished in '.($endtime-$starttime).' seconds. Stats of '.count($servers).' servers retrieved.');