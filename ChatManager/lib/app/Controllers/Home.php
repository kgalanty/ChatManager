<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers;
use WHMCS\Module\Addon\ChatManager\Controller;
use LiveChat\Api\Client as LiveChat;

/**
 * Admin Area Controller
 */
class Home extends Controller
{
     /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return array
     */
    public function __construct()
    {
         //parent::__construct();
    }
    public function index($vars)
    {
        
$LiveChatAPI = new LiveChat('0fce32f4-5759-4609-b155-4ec9435ed7b1', 'dal:-9MPtP5TFXnIbD8C1Dq3rqW8XX4');
$agents = $LiveChatAPI->agents->getArchives(['filters' => []]);
// echo('<pre>');
// var_dump($agents);
// echo('</pre>');
        //$vars['somevar'] = 'somevalue';
        return $vars;
    }
}