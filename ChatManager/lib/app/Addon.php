<?php
namespace WHMCS\Module\Addon\ChatManager\app;
use WHMCS\Database\Capsule as DB;
class Addon
{
    public static function config()
    {
        return [
            // Display name for your module
            'name' => 'Chat Manager (TBD)',
            // Description displayed within the admin interface
            'description' => '',
            // Module author name
            'author' => 'TMD',
            'version' => '1.0.0',
        ];
    }
    public static function activate()
    {
//        DB::statement('
//        CREATE TABLE IF NOT EXISTS `kg_cancelrequests` (
//         `id` int(10) NOT NULL AUTO_INCREMENT,
//         `date` datetime DEFAULT NULL,
//         `relid` int(10) NOT NULL,
//         `cr_id` int(11) NOT NULL,
//         `reason` text COLLATE utf8_unicode_ci NOT NULL,
//         `reason_ext` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//         `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//         `livechat_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//         `customoffer` int(11) DEFAULT NULL,
//         `agent` int(11) DEFAULT NULL,
//         `agent_confirmed` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//         `multiagents` int(11) DEFAULT -1,
//         `type` text COLLATE utf8_unicode_ci NOT NULL,
//         `note_relid` int(11) DEFAULT NULL,
//         PRIMARY KEY (`id`),
//         KEY `serviceid` (`relid`)
//       ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
//     DB::statement('CREATE TABLE IF NOT EXISTS `kg_invoiceprolong` (
//     `id` int(10) NOT NULL AUTO_INCREMENT,
//     `invoiceid` int(10) NOT NULL,
//     `relid` int(10) DEFAULT NULL,
//     PRIMARY KEY (`id`)
//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
// 	DB::statement('CREATE TABLE IF NOT EXISTS `kg_customoffers` (
//     `id` int(10) NOT NULL AUTO_INCREMENT,
//     `offer` text COLLATE utf8_unicode_ci NOT NULL,
//     PRIMARY KEY (`id`)
//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

//        ');
            return [
                'status' => 'success', 
                'description' => 'The module has been successfuly activated.',
            ];
        }
        public static function deactivate()
        {
            return [
                'status' => 'success', 
                'description' => 'The module has been successfuly deactivated.',
            ];
        }
        public static function upgrade()
        {
        }
    }