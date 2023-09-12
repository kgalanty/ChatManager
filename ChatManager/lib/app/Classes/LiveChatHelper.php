<?php

namespace WHMCS\Module\Addon\ChatManager\app\Classes;

use WHMCS\Module\Addon\ChatManager\app\Consts\LiveChatConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\DateTimeHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\LiveChatParsers;
use WHMCS\Module\Addon\ChatManager\app\Models\Threads;
use WHMCS\Module\Addon\ChatManager\app\Consts\AdminGroupsConsts;
use LiveChat\Api\Client as LiveChat;

class LiveChatHelper
{
    public $api;
    public $timezone;
    //$timezone moment().format('Z')
    //Intl.DateTimeFormat().resolvedOptions().timeZone
    public $results;
    public $dateFrom;
    public function __construct(string $datefrom = null, string $timezone = '')
    {
        $this->api = new LiveChat(LiveChatConsts::LIVECHAT_LOGIN, LiveChatConsts::LIVECHAT_PASS);

        if ($timezone == '') {
            $this->timezone = 'Europe/Sofia';
        } else {
            $this->timezone = $timezone;
        }
        $this->dateFrom = $datefrom ? $datefrom : DateTimeHelper::subDate($this->timezone, new \DateInterval('PT12H'))->format('Y-m-d\TH:i:s.000000P');
    }
    public function findChatByID(string $tid): array
    {
        if (Threads::where('threadid', $tid)->count() > 0) {
            return ['result' => 'This Thread ID already exists. Try with another one.'];
        }

        $params = ['filters' => ['thread_ids' => [$tid]]];
        $this->results =  $this->api->agents->getArchives($params);
        return ['result' => 'success', 'found_chats' => $this->results->found_chats];
    }
    public function readRecentChats(array $filtersSet = [], string $pageid = null)
    {
        $filters['from'] = $this->dateFrom;

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
        logActivity('Chat manager running. Date From: '.$filters['from'] .'. Chats count: '.$_SESSION['cmcount'].'/'.$this->results->found_chats);
        $this->runParseStore();

        if ($this->results->next_page_id) {
            $this->readRecentChats([], $this->results->next_page_id);
        }
    }
    public function runParseStore()
    {
        if ($this->results) {
            LiveChatParsers::parseArchiveList($this->results->chats);
        }
    }

    public static function getUserById(string $id, array $users)
    {
        foreach ($users as $user) {
            if ($user->id == $id) {
                return $user;
            }
        }
        return null;
    }
    public static function getAgentByPersonalTags($chatitem, $admins)
    {
        $intersection = array_intersect(AdminGroupsConsts::TAGSAGENTMAP, $chatitem->thread->tags);
        if (
            count($intersection) > 0 &&
            in_array('sales', $chatitem->thread->tags) &&
            (in_array('wcb', $chatitem->thread->tags) || in_array('directsale', $chatitem->thread->tags))
        ) {
            return array_search(array_values($intersection)[0], AdminGroupsConsts::TAGSAGENTMAP);
        }
        $agent = 0;
        foreach ($chatitem->thread->user_ids as $agent_id) {

            $agent = $admins[$agent_id] ? $admins[$agent_id]->id : $agent;
        }
        return $agent;
    }
}
