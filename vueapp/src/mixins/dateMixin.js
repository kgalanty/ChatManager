
export const dateMixin = {
    methods: {
        parseDateTime(dateTime) {
            return this.moment().utc(dateTime).format("YYYY-MM-DD HH:mm:ss");
        },
        parseDateTimeFromUTCtoLocal(dateTime) {
            return this.moment.utc(dateTime, 'YYYY-MM-DD HH:mm:ss').local().format("YYYY-MM-DD HH:mm:ss");
        },
        createUTCDatetime(datetime) {
            return (
              this.moment(datetime).utc().format("YYYY-MM-DDTHH:mm:SS") + ".000000Z"
            )
        }
    }
}
