<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\DateTimeHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatParsers;
use LiveChat\Api\Client as LiveChat;

class LiveChatHelper
{
    public $api;
    public $timezone;
    //$timezone moment().format('Z')
    //Intl.DateTimeFormat().resolvedOptions().timeZone
    public $results;
    public function __construct($timezone = '')
    {
        $this->api = new LiveChat(LiveChatConsts::LIVECHAT_LOGIN, LiveChatConsts::LIVECHAT_PASS);

        if ($timezone == '') {
            $this->timezone = 'Europe/Sofia';
        } else {
            $this->timezone = $timezone;
        }

        //$agents = $LiveChatAPI->agents->getArchives(['filters' => []]);
    }
    public function readRecentChats($date = null, array $filtersSet = [], string $pageid = null)
    {
        if ($date) {
            $filters['from'] = $date;
        } else {
            //2021-08-30T00:00:00.000000-02:00
            $filters['from'] = (DateTimeHelper::subDate($this->timezone))->format('Y-m-d\TH:i:s.000000P');
        }
        if ($pageid !== null) {
            $filters['pageid'] = $pageid;
        }
        $params['filters'] = array_merge($filters, $filtersSet);
        if ($pageid !== null) {
            $params['page_id'] = $pageid;
            unset($params['filters']);
            unset($params['limit']);
        }

        $this->results = $this->api->agents->getArchives($params);
       
        $this->runParseStore();
        if ($this->results->next_page_id) {
            $this->readRecentChats(null, [], $this->results->next_page_id);
        }
    }
    public function runParseStore()
    {
        if ($this->results) {
            LiveChatParsers::parseArchiveList($this->results->chats);
        }
    }
    public static function getUserById(string $id, array $users) : ?object
    {
        foreach($users as $user)
        {
            if($user->id == $id)
            {
                return $user;
            }
        }
        return null;
    }
}
