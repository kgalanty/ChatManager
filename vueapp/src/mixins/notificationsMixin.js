export default {
    methods: {
        abstractNotify(msg, type)
        {
            this.$buefy.notification.open({
                message: msg,
                type: 'is-'+type,
                duration: 5000,
                autoClose: true,
                closable: false
            })
        },
        notifyDanger(msg) {
            this.abstractNotify(msg, 'danger')
        },
        notifySuccess(msg)
        {
            this.abstractNotify(msg, 'success')
        },
        notifyWarning(msg)
        {
            this.abstractNotify(msg, 'warning')
        },
    }
}
