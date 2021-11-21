export default {
    methods: {
        showError(msg) {
            this.$buefy.dialog.alert({
                title: "Error",
                message: msg,
                type: "is-danger",
                hasIcon: true,
                icon: "close-circle",
                ariaRole: "alertdialog",
                ariaModal: true,
                confirmText: "Reload",
                onConfirm: () => window.location.reload(),
            });
        }
    }
}