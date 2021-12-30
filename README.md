# Chat Manager (WHMCS Addon)

## Installation

1. Copy `ChatManager` directory to `/modules/addons/` 

2. Navigate to WHMCS Admin Area panel -> Addons menu and activate it

## Front-end installation

1. Clone the project

2. Navigate to `vueapp` in terminal

3. Run the command
```
npm run build
```

4. When done, front-end is build in `/vueapp/modules` subdirectory -  It needs to be copied to the module's directory on server

5. Main template file is located in `/vueapp/dist/` - It goes to `\modules\addons\ChatManager\lib\app\Views`

## What it does

The module is responsible for maintaining LiveChat chats, keeps in sync (via cron) and manage them - Assign tags, operator, customer, Order ID/Invoice ID.

There are four tabs in the module:

1. Chats - Main chat table and red, pending for review chats table (only for operators in certain groups)

2. Statistics - It calculates points and shows statistics for all operators (when admin) or operator's data (when not admin)

3. Orders (Admin Only) - It gathers completed orders and reference to LiveChat visitors. Based on it, it's easier to match chats with orders later

4. Logs (Admin Only) - The module saves events in logs, matching with chats or tags. This tab shows them all to have an overview of what's happening in the system.

## Cron

There are two crons: 

1. statscron.php - Reads chats from LiveChat API. Should be set every 5 minutes or so in crontab.
2. orderscron.php - Tries to match chats in database with registered orders/clients and possibly match with orders listed in Orders tab.

## Configuration

Some functionalities are limited to admins only. The module requires to configure groups (roles) of staff from WHMCS to make this distinct.


### `lib/app/Consts/AdminGroupsConsts.php` 

- `AGENT` and `ADMIN` - both assign staff roles from WHMCS. 

- `AGENT_DISALLOWED` constant allows to exclusively disable the module for certain admin ID, even when within above groups.

- `TAGSAGENTMAP` - matches staff ID with their own tag.


### `lib/app/Consts/moduleVersion.php` 

- This file contains current version of the module. It must be in sync with currently used set in front-end. 

Value is stored in `versionMixin.js` - `packageversion`.

### `lib/app/Consts/LiveChatConsts.php` 

- This file contains LiveChat API credentials.


## Statistics

Statistics are calculated based on tags. The following list of tags counts as one point each:

- directsale
- upsell
- cycle
- vps/ds
- convertedsale

They count only if associated invoice is assigned. Also, when `upgrade` tag is present, entire chat counts for 1 no matter what other tags are there.

Also, if there's cancellation request with `stayed` result, it counts for additional 1 point.

Admins (from `AdminGroupsConsts`) can see stats of everyone, while non-Admins (`AGENT`) can see only their own stats.

# How are statistics columns calculated?

This is explained below using format `[column name]`: [formula]

- `Can Offer`: direct sale + wcb
- `Cannot Offer`: cannot offer ( cannot offer / (direct sale + wcb + cannot offer) * 100%)
- `Total Sales Chats`: direct sale + wcb + cannot offer
- `Direct Sales`: direct sale
- `Converted Sales`: converted sale
- `Upgrades`: upgrade
- `Total Sales`: direct sale + converted sale + upgrade
- `Upsell`: upsell
- `Cycle`: cycle
- `Stayed`: Cancellation Manager points
- `VPS/DS`: VPS/DS
- `Total Points`: direct sale + convertedsale + upsell + cycle + VPS/DS + Cancellation points + upgrade
- `Conversion without Points`: (Direct sale+converted sale + upgrade)*100% / (direct sale + wcb)
- `Conversion with Points`: (Direct sale+converted sale + upgrade + upsell + cycle + Cancellation points + VPS/DS)*100% / (direct sale + wcb)

## Orders

This tab lists completed orders. These records are stored after a cookie is set during customer's chat. Afterwards, when the customer completes the order (order is finished in WHMCS), the system correlates previous chat with the order and creates a record there.

It makes sense only if the chat is made before ordering a product, otherwise it might not work as expected.

In other edge cases, there are other mechanisms of matching chats with customers.

## Logs

Logs are located in `Logs` tab (Available for admins) when matched to `ADMIN` const.

It logs every action done in the system, with its author and date. You can filter by dates, operators and by chat id it belongs to.

When Agent is set to 'Admin Nickola', it means it's done by cron.
 
## Other development commands for front-end app

```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

