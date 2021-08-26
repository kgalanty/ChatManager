<?php

namespace WHMCS\Module\Addon\ChatManager\app\Controllers\API;

use WHMCS\Module\Addon\ChatManager\app\Controllers\API;
use WHMCS\Database\Capsule as DB;
//use WHMCS\Module\Addon\ChatManager\app\Models\Request as RequestModel;
use WHMCS\Module\Addon\ChatManager\app\Models\Request as RequestModel;
use WHMCS\Module\Addon\ChatManager\app\Models\CancelRequest;
use WHMCS\Module\Addon\ChatManager\app\Models\Clientgroup;
use WHMCS\Module\Addon\ChatManager\app\Models\Admin;
use WHMCS\Module\Addon\ChatManager\app\Models\InvoiceProlong;
use WHMCS\Module\Addon\ChatManager\app\Models\Service;
use WHMCS\Module\Addon\ChatManager\app\Models\Product;
use WHMCS\Module\Addon\ChatManager\app\Models\InvoiceItem;
use WHMCS\Module\Addon\ChatManager\app\Classes\EmailTemplatesConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\GidsConsts;
use WHMCS\Module\Addon\ChatManager\app\Classes\EmailHelper;
use WHMCS\Module\Addon\ChatManager\app\Classes\Invoices as InvoicesClass;
use WHMCS\Module\Addon\ChatManager\app\Classes\DomainsHelper;
use WHMCS\Module\Addon\ChatManager\app\Models\Note;
use WHMCS\Module\Addon\ChatManager\app\Classes\NotesHelper;

class Request extends API
{
    public function get()
    {

        if ($_GET['action'] == 'getReasons') {
            $cr = CancelRequest::where('id', $_GET['cr_id'])->first(['id', 'reason', 'reason_ext']);
            return ['data' => $cr];
        }

        $legacyPids =
            [
                'vps'
                => [
                    27, 166, 167, 170, 168, 171, 173, 297, 296, 294, 298, 295, 28, 55, 192, 255, 56, 137, 151, 150, 155, 164, 153, 157, 165, 156, 160, 220, 159, 163, 100, 158, 162, 122, 126, 152, 138,
                    119, 23, 51, 120, 118, 236, 238, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 144, 146, 250, 147, 251, 148, 149, 183, 184, 186
                ],
                'shared' =>
                [
                    225, 226, 190, 196, 145, 224, 206, 198, 197, 205, 213, 96, 195, 175, 142, 187, 202, 215, 179, 201, 204, 200, 199, 88, 203, 131, 176, 94, 221, 193, 194
                ],
                'extra' => [
                    67, 71
                ]
            ];

        $page = $_GET['page'] == 1 ? 0 : ($_GET['page'] - 1) * $_GET['perpage'];
        $vpsdsGids = GidsConsts::VPSGIDS;
        $sharedGids = GidsConsts::SHAREDGIDS;
        $extraServicesGids = GidsConsts::EXTRASERVICESGIDS;
        //return Clients::with(['clientgroup'])->get(); 'service', 'service.client', 
        //var_dump(RequestModel::with('service.client.clientgroup')->get()); die;
        //    $r = [];
        //    $requests = RequestModel::has('service')
        //    ->has('service.client')
        //    ->has('service.client.clientgroup')
        //    ->with('service')
        //    ->with('service.client.clientgroup')
        //    ->limit(10)
        //    ->get();
        $tblhosting = [
            'h.userid',
            'h.orderid',
            'h.packageid',
            'h.server',
            'h.regdate',
            'h.domain',
            // 'h.paymentmethod',
            // 'h.amount',
            'h.billingcycle',
            'h.nextduedate',
            'h.nextinvoicedate',
            'h.termination_date',
            'h.completed_date',
            'h.domainstatus',
            'h.subscriptionid',
        ];
        $tblclients = [
            'c.firstname',
            'c.lastname',
            'c.companyname',
            'c.email',
            'c.groupid',
            'c.phonenumber',
            'c.country'

        ];
        $tblclientgroups = ['cg.groupname'];
        $tblproducts = ['p.name AS productname', 'p.gid'];
        $requests = DB::table('tblcancelrequests as cr')
        	->join('kg_cancelrequests as kgcr', 'kgcr.cr_id', '=','cr.id')
            ->join('tblhosting as h', 'h.id', '=', 'cr.relid')
            ->join('tblproducts as p', 'p.id', '=', 'h.packageid')
            ->join('tblclients as c', 'c.id', '=', 'h.userid')
            ->join('tblclientgroups as cg', 'cg.id', '=', 'c.groupid');
        if ($_GET['type'] == 'vip') {
            $requests = $requests->where('cg.groupname', 'LIKE', 'VIP%')
                ->whereNotIn("h.domainstatus", ["Cancelled", "Terminated"]);
        } elseif ($_GET['type'] == 'vps') {
            $requests = $requests->where('cg.groupname', 'NOT LIKE', 'VIP%')
                ->whereIn('p.gid', $vpsdsGids)
                ->orWhereIn('h.packageid', $legacyPids['vps'])
                ->whereNotIn("h.domainstatus", ["Cancelled", "Terminated"]);
        } elseif ($_GET['type'] == 'shared') {
            $requests = $requests->where('cg.groupname', 'NOT LIKE', 'VIP%')
                ->where(function ($query) use ($sharedGids, $legacyPids) {
                    $query->whereIn('p.gid', $sharedGids)->orWhereIn('p.id', $legacyPids['shared']);
                })
                ->whereNotIn("h.domainstatus", ["Cancelled", "Terminated"]);
        } elseif ($_GET['type'] == 'completed') {
             $requests = DB::table('kg_cancelrequests as cr')
            ->join('tblhosting as h', 'h.id', '=', 'cr.relid')
            ->join('tblproducts as p', 'p.id', '=', 'h.packageid')
            ->join('tblclients as c', 'c.id', '=', 'h.userid')
            ->join('tblclientgroups as cg', 'cg.id', '=', 'c.groupid');
            $requests = $requests->whereIn('h.domainstatus', ["Cancelled", "Terminated"]);
            if ($_GET['filterDomain'] != '') {
                $requests = $requests->where('h.domain', 'LIKE', $_GET['filterDomain'] . '%')
                    ->orWhere('c.email', 'LIKE',  $_GET['filterDomain'] . '%');
            }
        } elseif ($_GET['type'] == 'extra') {
            $requests = $requests->where('cg.groupname', 'NOT LIKE', 'VIP%')
                ->where(function ($query) use ($vpsdsGids, $sharedGids) {
                    $query->whereNotIn('p.gid', $vpsdsGids)
                        ->whereNotIn('p.gid', $sharedGids);
                })
                ->orWhereIn('h.packageid', $legacyPids['extra'])
                ->whereNotIn("h.domainstatus", ["Cancelled", "Terminated"]);
        }
        //  $secondQr = clone $requests;
        $total = $requests->count();
        $requests = $requests->orderBy('cr.' . $_GET['sort'], $_GET['order'])->select(array_merge([
            'cr.*'
        ], $tblhosting, $tblclients, $tblclientgroups, $tblproducts));
        //$secondQr = $secondQr->union($requests)->where('cr.date', '<=', DB::raw('DATE_SUB(NOW(), INTERVAL 3 day)'));



        //$common = [];
        //$rqCount = $requests->count();
        //if ($rqCount < $_GET['perpage']) {
        // $common = clone $requests;
        // if ($rqCount == 0) {
        //     $common = $requests->skip($page)->take($_GET['perpage']);
        // }
        // $common = $common->take($_GET['perpage'] - $rqCount)->where('cr.date', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 3 day)'))->orderBy('id', 'DESC')
        //     ->get(array_merge([
        //         'cr.*'
        //     ], $tblhosting, $tblclients, $tblclientgroups, $tblproducts));
        //}
            //   if($_SESSION['adminid'] == 230)
            // {
            // 	echo('<pre>'); var_dump($requests->toSql());die;
            // }
        $requests = $requests->skip($page)->take($_GET['perpage'])
            ->get(array_merge([
                'cr.*'
            ], $tblhosting, $tblclients, $tblclientgroups, $tblproducts));

        foreach ($requests as &$req) {
            $req->tickets = DB::table('tbltickets')->where('userid', $req->userid)->orderBy('id', 'DESC')->limit(5)->get(['id', 'tid', 'title']);
            if (CancelRequest::where('relid', $req->relid)->where('action', 'delete')->count() > 0) {
                $req->urgent = 1;
            }
        }
        return ['total' => $total, 'data' => $requests];
    }
    private function saveReasons(int $cr_id, $r1, $r2)
    {
        CancelRequest::where('id', $cr_id)->update(['reason' => $r1, 'reason_ext' => $r2]);
        return 'success';
    }
    public function post()
    {
        //array(8) {
        //   ["type"]=>
        //   string(6) "Stayed"
        //   ["r1"]=>
        //   string(28) "I no longer need the service"
        //   ["r2"]=>
        //   string(10) "Never used"
        //   ["hid"]=>
        //   string(5) "52396"
        //   ["lid"]=>
        //   string(9) "asdasdasd"
        //   ["crid"]=>
        //   string(5) "17767"
        //   ["ctype"]=>
        //   string(21) "End of Billing Period"
        //   ["magents"]=>
        //   string(4) "true"
        // }
        if (!$this->input['data']) return;
        $admin = Admin::find($_SESSION['adminid']);
        $action = strtolower($this->input['data']['type']);
        $hid = intval($this->input['data']['hid']);
        $crid = $this->input['data']['crid'];
        $r2 = $this->input['data']['r2'];
        $custom_offer = $this->input['data']['co'];


        if ($this->input['data']['action'] == 'saveReasons') {
            return $this->saveReasons($crid, $this->input['data']['r1'], $this->input['data']['r2']);
        }

        if (CancelRequest::where('cr_id', $crid)->count() == 0) {

            if ($crid) {
                $originalRequest = RequestModel::where('id', $crid)->first();
            } else {
                $originalRequest = RequestModel::where('relid', $hid)->first();
            }
            $clientnote = NotesHelper::AddNote(
                DB::table('tblhosting')->where('id', $hid)->value('userid'),
                'A cancellation request exists for this item, so it will not be invoiced when due. Reason: ' . $this->input['data']['r1']
            );
            CancelRequest::create([
                'date' => date('Y-m-d H:i:s'),
                'relid' => (int)$hid,
                'cr_id' => (int)$crid,
                'reason' => $originalRequest->reason,
                'reason_ext' => $r2,
                'multiagents' => $this->input['data']['magents'] ? 1 : 0,
                'type' => $originalRequest->type,
                'note_relid' => $clientnote['noteid']
            ]);
        }
        //InvoiceProlong::create(['invoiceid' => 357031, 'relid'=> $hid]);

        switch ($action) {
            case 'stayed':

                $inv = InvoicesClass::getNewestUnpaidInvoiceByHid($hid);
                if ($inv) {
                    if (InvoiceProlong::where('invoiceid', $inv)->count() > 0) {
                        InvoicesClass::ShortenDueDateOfInvoice($inv, 4);
                        InvoiceProlong::where('invoiceid', $inv)->delete();
                    }
                }
                $serviceDetails = Service::where('id', $hid)->first();
                NotesHelper::AddNote(
                    Service::where('id', $hid)->value('userid'),
                    'The customer requested a cancellation for #' . $hid . ' ' . $serviceDetails->product->name . ' - ' . $serviceDetails->domain . ' but decided to stay.'
                );
                //Restore cancelled domain renewal invoice
                DomainsHelper::revertDomainRenewalInvoiceForHid($hid);

                $crequest_kg = CancelRequest::where('cr_id', $this->input['data']['crid'])->first();
                if ($crequest_kg->note_relid) {
                    NotesHelper::Unstick($crequest_kg->note_relid);
                    //Note::where('id', $crequest_kg->note_relid)->update(['sticky' => '0']);
                }
                RequestModel::where('id', $crid)->delete();
                CancelRequest::where('cr_id', $crid)->update(
                    [
                        'reason_ext' => $r2,
                        'livechat_id' => $this->input['data']['lid'],
                        'multiagents' => $this->input['data']['magents'] ? 1 : 0,
                        'action' => $action,
                        'agent' => $_SESSION['adminid'],
                        'customoffer' => $custom_offer
                    ]
                );
                logActivity('Cancellation Manager: ' . $admin->firstname . ' ' . $admin->lastname . ' changed request status to \'Stayed\' - Service #' . $hid . '');
                return 'success';
                break;

            case 'left':

                $invoices = InvoiceItem::whereHas('addon', function (\Illuminate\Database\Eloquent\Builder $query) use ($hid) {
                    $query->where('hostingid', $hid);
                })
                    ->onlyAddons()->distinct()->select('invoiceid')->get();
                if ($invoices) {
                    \WHMCS\Billing\Invoice::whereIn('id', $invoices)->unpaid()->update(['status' => 'Cancelled']);
                }


                $addons = DB::table("tblhostingaddons")->where("hostingid", "=", $hid)->where("status", "!=", 'Cancelled')
                    ->leftJoin("tbladdons", "tbladdons.id", "=", "tblhostingaddons.addonid")
                    ->get(array(DB::raw("tblhostingaddons.id"), "userid", "status", "module"));


                if ($addons) {
                    if (!function_exists('processAddonsCancelOrFraud')) {
                        require_once ROOTDIR . '/includes/orderfunctions.php';
                    }
                    $cancelResult = \processAddonsCancelOrFraud($addons, 'Cancelled');
                }

                $serviceDetails = Service::where('id', $hid)->first();
                $gid = $serviceDetails->product->gid;
                $cancel_request = DB::table('tblcancelrequests')->where('relid', $hid)->first();


                NotesHelper::AddNote(
                    $serviceDetails->userid,
                    'The customer requested a cancellation for #' . $serviceDetails->id . ' ' . $serviceDetails->product->name . ' - ' . $serviceDetails->domain . ' which was successfully completed'
                );

                if (!function_exists('cancelSubscriptionForService')) {
                    require_once ROOTDIR . '/includes/gatewayfunctions.php';
                }
                $crequest_kg = CancelRequest::where('cr_id', $this->input['data']['crid'])->first();
                if ($crequest_kg->note_relid) {
                    NotesHelper::Unstick($crequest_kg->note_relid);
                    //Note::where('id', $crequest_kg->note_relid)->update(['sticky' => '0']);
                }
                if (in_array($gid, GidsConsts::SHAREDGIDS)) {
                    // - if the canceled account is shared hosting and the cancellation type is immediate - Suspend the account; 
                    //set its status to canceled; 
                    //cancel any invoice associated with its renewal; 
                    //If there is an active PayPal subscription for the account the system should attempt to cancel it; 
                    //Disable the auto-renewal of the associated domain if this has been selected during the cancellation request; 
                    //Leave a note in the customer account: The customer requested a cancellation which was successfully completed; 
                    //Send the 02 - Cancellation request confirmation email;

                    // - if the canceled account is shared hosting and the cancellation type is End of billing cycle - Set the account to be terminated at the end of the billing cycle; 
                    //cancel any invoice associated with its renewal; If there is an active PayPal subscription for the account the system should attempt to cancel it; 
                    //Disable the auto-renewal of the associated domain if this has been selected during the cancellation request; 
                    //Leave a note in the customer account: The customer requested a cancellation which was successfully completed; 
                    //Send the 02 - Cancellation request confirmation email;
                    if ($cancel_request->type == 'Immediate' || $cancel_request->type == 'End of Billing Period') {
                        Service::where('id', $hid)->update(['domainstatus' => 'Cancelled']);
                        EmailHelper::SendEmailByTpl(EmailTemplatesConsts::CancellationRequestConfirmationEmail, $hid);
                    }
                    if ($cancel_request->type == 'Immediate') {

                        localAPI('ModuleSuspend', ['serviceid' => $hid]);
                        //Service::where('id', $hid)->update(['domainstatus' => 'Cancelled']);
                        InvoicesClass::CancelAnyUnpaidInvoiceByHid($hid);
                        if ($serviceDetails->subscriptionid != '') {
                            try {
                                cancelSubscriptionForService($hid, $serviceDetails->userid);
                            } catch (\Exception $e) {
                                switch (get_class($e)) {
                                    case "WHMCS\\Exception\\Gateways\\SubscriptionCancellationNotSupported":
                                        $er = 'Cancel subscription not supported by the gateway';
                                        break;
                                    case "WHMCS\\Exception\\Gateways\\SubscriptionCancellationFailed":
                                        $er = 'Cancel subscription refused by the gateway';
                                        break;
                                    case "InvalidArgumentException":
                                        $er = 'Invalid argument';
                                        break;
                                }
                            }
                        }

                        // NotesHelper::AddNote(
                        //     $serviceDetails->userid,
                        //     'The customer requested a cancellation for #'.$serviceDetails->id.' '.$serviceDetails->product->name.' - '.$serviceDetails->domain.' which was successfully completed'
                        // );

                        //EmailHelper::SendEmailByTpl(EmailTemplatesConsts::CancellationRequestConfirmationEmail, $hid);
                        RequestModel::where('id', $crid)->delete();
                        CancelRequest::where('cr_id', $crid)->update(
                            [
                                'reason_ext' => $r2,
                                'livechat_id' => $this->input['data']['lid'],
                                'multiagents' => $this->input['data']['magents'] ? 1 : 0,
                                'action' => $action,
                                'agent' => $_SESSION['adminid'],
                                'customoffer' => $custom_offer
                            ]
                        );
                        return $er ? $er : 'success';
                    } elseif ($cancel_request->type == 'End of Billing Period') {
                        //localAPI('ModuleSuspend', ['serviceid' => $hid]);
                        //Service::where('id', $hid)->update(['domainstatus' => 'Cancelled']);
                        InvoicesClass::CancelAnyUnpaidInvoiceByHid($hid);
                        EmailHelper::SendEmailByTpl(EmailTemplatesConsts::CancellationRequestConfirmationEmail, $hid);
                        EmailHelper::SendCustomEmail(EmailTemplatesConsts::BillingEmail, EmailTemplatesConsts::BillingSubject, EmailTemplatesConsts::BillingMessage, 
                    ['termination_cycle' => $cancel_request->type, 'link'=>
                    \App::getSystemUrl() . \App::get_admin_folder_name() . "/clientsservices.php?userid=" . $serviceDetails->userid . "&id=" . $hid]);

                        RequestModel::where('id', $crid)->delete();

                        CancelRequest::where('cr_id', $crid)->update(
                            [
                                'reason_ext' => $r2,
                                'livechat_id' => $this->input['data']['lid'],
                                'multiagents' => $this->input['data']['magents'] ? 1 : 0,
                                'action' => $action,
                                'agent' => $_SESSION['adminid'],
                                'customoffer' => $custom_offer
                            ]
                        );
                        //EmailHelper::SendEmailByTpl(EmailTemplatesConsts::CancellationRequestConfirmationEmail, $hid);
                        return $er ? $er : 'success';
                    } else
                        return 'Cancel request not found';
                } elseif (in_array($gid, GidsConsts::VPSGIDS)) {
                    if ($cancel_request->type == 'Immediate' || $cancel_request->type == 'End of Billing Period') {

                        if ($serviceDetails->subscriptionid != '') {
                            try {
                                cancelSubscriptionForService($hid, $serviceDetails->userid);
                            } catch (\Exception $e) {
                                switch (get_class($e)) {
                                    case "WHMCS\\Exception\\Gateways\\SubscriptionCancellationNotSupported":
                                        $er = 'Cancel subscription not supported by the gateway';
                                        break;
                                    case "WHMCS\\Exception\\Gateways\\SubscriptionCancellationFailed":
                                        $er = 'Cancel subscription refused by the gateway';
                                        break;
                                    case "InvalidArgumentException":
                                        $er = 'Invalid argument';
                                        break;
                                }
                            }
                        }

                        localAPI('ModuleSuspend', ['serviceid' => $hid]);
                        DB::table('tblhosting')->where('id', $hid)->update(['domainstatus' => 'Cancelled']);

                        DB::table('tblinvoices')->whereIn('id', function ($query) use ($hid) {
                            $query->select('invoiceid')->from('tblinvoiceitems')->whereRaw("type='Hosting' AND relid='" . (int) $hid . "'");
                        })
                            ->update(['tblinvoices.status' => 'Cancelled']);

                        //NotesHelper::AddNote($serviceDetails->userid, 'The customer requested a cancellation for #'.$serviceDetails->id.' '.$serviceDetails->product->name.' - '.$serviceDetails->domain.' which was successfully completed');

                        EmailHelper::SendEmailByTpl(EmailTemplatesConsts::CancellationRequestConfirmationEmail, $hid);
                        EmailHelper::SendCustomEmail(EmailTemplatesConsts::BillingEmail, EmailTemplatesConsts::BillingSubject, EmailTemplatesConsts::BillingMessage, 
                    ['termination_cycle' => $cancel_request->type, 'link'=>
                    \App::getSystemUrl() . \App::get_admin_folder_name() . "/clientsservices.php?userid=" . $serviceDetails->userid . "&id=" . $hid]);
                        RequestModel::where('id', $crid)->delete();
                        CancelRequest::where('cr_id', $crid)->update(
                            [
                                'reason_ext' => $r2,
                                'livechat_id' => $this->input['data']['lid'],
                                'multiagents' => $this->input['data']['magents'] ? 1 : 0,
                                'action' => $action,
                                'agent' => $_SESSION['adminid']
                            ]
                        );
                        logActivity('Cancellation Manager: ' . $admin->firstname . ' ' . $admin->lastname . ' changed request status to \'Left\' - Service #' . $hid . '');

                        return $er ? $er : 'success';
                    } else
                        return 'Cancel request not found';
                }
                // - If the canceled account is vps/ds and the cancellation type is immediate - Send an email to billing@tmdhosting.net ( the email text will be provided later, for now it can be lorem ipsum; cancel any invoice associated with its renewal; If there is an active PayPal subscription for the account the system should attempt to cancel it; Disable the auto-renewal of the associated domain if this has been selected during the cancellation request; Leave a note in the customer account: The customer requested a cancellation which was successfully completed; Send the 02 - Cancellation request confirmation email;
                // - If the canceled account is vps/ds and the cancellation type is End of billing cycle - Send an email to billing@tmdhosting.net ( the email text will be different from the one above and provided later, for now it can be lorem ipsum 222; cancel any invoice associated with its renewal; If there is an active PayPal subscription for the account the system should attempt to cancel it; Disable the auto-renewal of the associated domain if this has been selected during the cancellation request; Leave a note in the customer account: The customer requested a cancellation which was successfully completed; Send the 02 - Cancellation request confirmation email;

                break;
            case 'delete':

                $crequest_kg = CancelRequest::where('cr_id', $crid)->first();
                if ($crequest_kg->note_relid) {
                    NotesHelper::Unstick($crequest_kg->note_relid);
                    //Note::where('id', $crequest_kg->note_relid)->update(['sticky' => '0']);
                }

                DB::table('tblcancelrequests')->where('relid', $hid)->delete();

                $serviceDetails = Service::where('id', $hid)->first();
                NotesHelper::AddNote(
                    $serviceDetails->userid,
                    'The customer requested a cancellation for #' . $hid . ' ' . $serviceDetails->product->name . ' - ' . $serviceDetails->domain . '. The cancellation was not confirmed and thus it was deleted.'
                );

                logActivity('Cancellation Manager: ' . $admin->firstname . ' ' . $admin->lastname . ' deleted request for service #' . $hid);
                $inv = InvoicesClass::getNewestUnpaidInvoiceByHid($hid);
                if ($inv) {
                    if (InvoiceProlong::where('invoiceid', $inv)->count() > 0) {
                        InvoicesClass::ShortenDueDateOfInvoice($inv, 4);
                    }
                }
                //Restore cancelled domain renewal invoice
                DomainsHelper::revertDomainRenewalInvoiceForHid($hid);

                if ($this->input['data']['sendemail']) {
                    $emailtplid = DB::table('tblemailtemplates')->where('name', EmailTemplatesConsts::CancellationRequestDeletedEmail)->value('id');
                    $emailTemplate = \WHMCS\Mail\Template::find($emailtplid);
                    $result = sendMessage($emailTemplate, $hid, "", true);
                }
                CancelRequest::where('cr_id', $crid)->update(
                    [
                        'action' => $action,
                        'agent' => $_SESSION['adminid']
                    ]
                );
                return 'success';
                break;
            case 'deleteextra':
                $crequest_kg = CancelRequest::where('cr_id', $crid)->first();
                if ($crequest_kg->note_relid) {
                    NotesHelper::Unstick($crequest_kg->note_relid);
                    //Note::where('id', $crequest_kg->note_relid)->update(['sticky' => '0']);
                }


                DB::table('tblcancelrequests')->where('relid', $hid)->delete();
                CancelRequest::where('cr_id', $crid)->update(
                    [
                        'action' => 'delete',
                        'agent' => $_SESSION['adminid']
                    ]
                );
                return 'success';
                logActivity('Cancellation Manager: ' . $admin->firstname . ' ' . $admin->lastname . ' deleted request - Service #' . $hid . '');
                break;
            case 'completeextra':
                $crequest_kg = CancelRequest::where('cr_id', $crid)->first();
                if ($crequest_kg->note_relid) {
                    NotesHelper::Unstick($crequest_kg->note_relid);
                    //Note::where('id', $crequest_kg->note_relid)->update(['sticky' => '0']);
                }
                DB::table('tblhosting')->where('id', $hid)->update(['domainstatus' => 'Cancelled']);

                DB::table('tblcancelrequests')->where('relid', $hid)->delete();
                CancelRequest::where('cr_id', $crid)->update(
                    [
                        'action' => 'left',
                        'agent' => $_SESSION['adminid']
                    ]
                );
                logActivity('Cancellation Manager: ' . $admin->firstname . ' ' . $admin->lastname . ' completed request - Service #' . $hid . '');
                return 'success';
                break;
        }
        return;
    }
}
