
export default {
    data() {
        return {
            packageversion: "1.0.12",
            build: "2021-11-30 00:21 UTC",
            logmessage: `
            <ul>
            <li><strong>1.0.12</strong>
            <li>- Removed checkpoint for duplicated order id when saving chat in modal</li>
            <li>- Adjusted tags margins and filter fields width</li>
            <li>- Added invoice id switch in the modal. Now you can explicitly set invoice id instead of order id</li>
            <li>- Above is still in testing phase, let me know if there's any bug :)</li>
            <li><strong>1.0.11</strong></li>
            
            <li>- Aligned Follow up buttons to middle</li>
            <li>- Adjusted width of some columns in both tables</li>
            <li> <strong>1.0.10</strong></li>
            <li>- Fixed calendars & dates</li>
            <li>- Adjusted width of some columns</li>
            <li>- Fixed calculating Follow ups upon applying filters</li>
            <li>- Fixed matching cron</li></ul>`
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
}
