export default {
    data() {
        return {
            packageversion: "1.0.20",
            build: "2021-12-20 13:17 UTC",
            logmessage: `
            <ul>
            <li><strong>1.0.20</strong></li>
            <li>- Added export statistics to pdf</li>
            <li>- Some small styling adjustments</li>
            <li><strong>1.0.19</strong></li>
            <li>- Added info in chat edit modal when it has 'upgrade' tag and no order/invoice id, advising to add invoice id</li>
            <li>- Added filtering in logs tab</li>
            <li>- Added support for multiple emails in email field - separated by comma - to be matched with possible order</li>
            <li><strong>1.0.18</strong></li>
            <li>- Fixed calculating points in chat table when in certain cases was failing</li>
            <li>- Fixed showing order/invoice id when accepting suggestion going to correct field</li>
            <li>- Added info about loading on all tables</li></ul>`
            // <li><strong>1.0.17</strong></li>
            // <li>- Fixed calculating points for upgrade chats with order and paid invoice in it</li>
            // <li>- Applied new cron looking for 'wcb' tags and adding 'convertedsale' when related order is paid (removing 'pending')</li>
            // <li>- Logs tab: Changed showing thread id instead of chat id not showing anywhere else</li>
            // <li>- Orders Tab: Added showing domain renewals & registers within orders</li>
            // <li>- Fixed setting incorrect page when coming back to Chats tab from another tab, when filtering was enabled before</li>
            // <li>- Added showing invoice status when it's different than 'Paid' in Chats tab</li>
            // <li>- Added info about pending reviews in red 'pending' table</li>`
            // <li><strong>1.0.16</strong></li>
            // <li>- Fixed chats not refreshing after using 'follow up' button</li>
            // <li>- Fixed calculating points for 'upgrade' tags</li>
            // <li>- Reorganized constant classes on backend</li>
            // <li>- Fixed logs for tags not showing in main chat log</li>
            // <li>- Message about empty result is now centered</li>
            // </ul>`
            // <li><strong>1.0.15</strong></li>
            // <li>- Fixed x-mark in order id field showing when invoice id is set</li>
            // <li>- Fixed logging changes in chats and saving when certain fields were cleared</li>
            // <li><strong>1.0.14</strong></li>
            // <li>- Added Logs tab for admins which aggregates all logs across the module and display in one place</li>
            // <li>- Adjusted some text labels</li>
            // <li>- Deleting unassigned orders now works</li>
            // <li><strong>1.0.13</strong></li>
            // <li>- Fixed showing Country and IP in main table</li>
            // <li>- Added functionality to inform when new version is published. When new version is detected on the server, you will see a notification asking to refresh page</li>
            // <li><strong>1.0.12</strong></li>
            // <li>- Removed checkpoint for duplicated order id when saving chat in modal</li>
            // <li>- Adjusted tags margins and filter fields width</li>
            // <li>- Added invoice id switch in the modal. Now you can explicitly set invoice id instead of order id</li>
            // <li>- Above is still in testing phase, let me know if there's any bug :)</li></ul>`
            // <li><strong>1.0.11</strong></li>
            
            // <li>- Aligned Follow up buttons to middle</li>
            // <li>- Adjusted width of some columns in both tables</li>
            // <li> <strong>1.0.10</strong></li>
            // <li>- Fixed calendars & dates</li>
            // <li>- Adjusted width of some columns</li>
            // <li>- Fixed calculating Follow ups upon applying filters</li>
            // <li>- Fixed matching cron</li></ul>`
            // <li><strong>1.0.9</strong></li>
          
            // <li>- Added Exclude Tags functionality</li>
            // <li>- Fixed '#blackfriday' tag in filters</li> </ul>`
            // <li> <strong>1.0.8</strong></li>
            // <li>- Changed text in staff online to 'now'</li>
            // <li>- Added sum of all agents in statistics</li>
            // <li>- Changed 'domain' column width</li>
            // <li><strong>1.0.7:</strong></li>
            // <li>- Added styling for pending orders in red table</li>
            // <li>- New functionality on the footer showing staff online in Chat Manager in recent 20 minutes</li>
            // <li>- Added new log entry when thread is imported by cron</li>
            // <li>- Fixed counting added entries count by cron in WHMCS logs</li> </ul>`
            // <li><strong>1.0.6:</strong></li>
            // <li>- Quick hotfix of margins between tables and headers</li>
            // <li><strong>1.0.5:</strong></li>
            // <li>- Fixed reading red table chats after leaving Edit Modal</li>
            // <li>- Added new tab "Orders" with orders with no chat assigned (only Admins)</li>
            // <li>- Small style adjustments</li>  `
            // <li><strong>Older log:</strong></li>
            // <li>- Added posibility to remove chat (for admins). This is testing functionality.</li>
            // <li>- Fixed directly opening stats by url</li>
            // <li>- Fixed cron assigning an Operator based on personal tag when paired with one of 'directsale'/'wcb' tag</li>
            // <li>- Adjusted some colors by the way of implementing dark mode. When it's done, current colors will become 'bright' side :)</li>
         
            // logmessage: `Recent Changes: <ul><li>
            // - Reordered columns in stats (Total Points & Conversion w/o points)
            // </li>
            // <li>
            // - Fix for calculating stayed points
            // </li>
            // </ul>`
            // logmessage: `Recent Changes: <ul><li>
            // - Adjusted maths in stats
            // </li>
            // <li>
            // - Minor color adjustments
            // </li>
            // </ul>`
            // logmessage: `Recent Changes: 1.0.1<ul><li>
            // - Changed some colors a bit - on the way to implement dark mode
            // </li>
            // <li>- Made calendars show dates in one fixed format dd.mm.yyyy</li>
            // <li>- Added auto-fill DateFrom calendar to current date in chats table</li>
            // <li>- Added auto-fill dates to current month in stats</li>
            // <li>- In backend: 'upgrade' tag is counted only when assigned to paid invoice</li>
            // </ul>`
        };
    },
    methods: {
        verifyVer(apiappver) {
           if(apiappver != this.packageversion)
           {
               return true
           }
           return false
        },
        appVerInconsistencyWarn()
        {
            this.$buefy.toast.open({
                container: ".modal-card",
                message:
                  "New version available. Please refresh the page when possible.",
                type: "is-success",
                indefinite: true,
                position: "is-bottom",
              });
        }
    }
}