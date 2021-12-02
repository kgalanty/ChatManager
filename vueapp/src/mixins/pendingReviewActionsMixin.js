import requestsMixin from './requestsMixin'
export const pendingReviewActionsMixin = {
    data() {
        return {
            hideMessage: false
        };
      },
    methods: {
        ConfirmDuplicatedChat(threadid) {

            const params = [`module=ChatManager`, `c=DuplicateReview`, `json=1`].join("&");
            this.$api
                .post(`addonmodules.php?${params}`, {
                    threadid: threadid,
                    a: 'SingleChat'
                })
                .then((response) => {
                    if (response.data == "success") {
                        this.hideMessage = true
                        this.notifySuccess('Successfuly marked')
                    } else {
                        this.notifyWarning(response.data)
                    }
                });

        },
        ConfirmDuplicatedAllChats(orderid) {
            const params = requestsMixin.methods.generateParamsForRequest('DuplicateReview')
            this.$api
                .post(`addonmodules.php?${params}`, {
                    orderid: orderid,
                    a: 'AllChatsWithGivenOrder'
                })
                .then((response) => {
                    if (response.data == "success") {
                        this.hideMessage = true
                        this.notifySuccess('Successfuly marked')

                    } else {
                        this.notifyWarning(response.data)
                    }

                });

        }
    }
}
