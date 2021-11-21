
export const cannotofferMixin = {
    data() {
        return {
            //Here you can edit cannot offer reasons current list
            cannotofferReasons: [
                "Pricing ( Reseller/Starter Shared)",
                "Accepting Bitcoins/Cryptocurrencies",
                "Django ( on Shared*)",
                ".js (on Shared*)",
                "Python (on Shared*)",
                "Crystal Reports (on Shared*)",
                "Java (on Shared*)",
                "Gambling websites",
                "Root access (shared/cloud*)",
                "IP range/multiple IPs",
                "Unlimited Inodes shared/cloud",
                "Storage size",
                "Unmanaged VPS",
                "Mongo DB",
                "Dedicated IP (shared/cloud)",
                "Over 1 GB database shared/cloud",
                "Mailbox sizes (too small for shared/cloud)",
                "MVC access on Win shared",
                "Other",
            ],
        };
    },
}
