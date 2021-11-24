
export default {
    data() {
        return {
            packageversion: "1.0.7",
            build: "2021-11-24 21:18 UTC",
            logmessage: `<strong>1.0.7:</strong><ul>
            <li>- Added styling for pending orders in red table</li>
            <li>- New functionality on the footer showing staff online in Chat Manager in recent 20 minutes</li>
            <li>- Added new log entry when thread is imported by cron</li>
            <li>- Fixed counting added entries count by cron in WHMCS logs</li>
            <li><strong>1.0.6:</strong></li>
            <li>- Quick hotfix of margins between tables and headers</li>
            <li><strong>1.0.5:</strong></li>
            <li>- Fixed reading red table chats after leaving Edit Modal</li>
            <li>- Added new tab "Orders" with orders with no chat assigned (only Admins)</li>
            <li>- Small style adjustments</li>
            <li><strong>Older log:</strong></li>
            <li>- Added posibility to remove chat (for admins). This is testing functionality.</li>
            <li>- Fixed directly opening stats by url</li>
            <li>- Fixed cron assigning an Operator based on personal tag when paired with one of 'directsale'/'wcb' tag</li>
            <li>- Adjusted some colors by the way of implementing dark mode. When it's done, current colors will become 'bright' side :)</li>
            </ul>`
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
