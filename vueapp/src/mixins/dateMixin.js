import moment from "moment";

export const dateMixin = {
    methods: {
        parseDateTime(dateTime) {
            return this.moment().utc(dateTime).format("YYYY-MM-DD HH:mm:ss");
        },
        parseDate(date) {
            return this.moment(date).format("YYYY-MM-DD");
        },
        parseDateTimeFromUTCtoLocal(dateTime) {
            return this.moment.utc(dateTime, 'YYYY-MM-DD HH:mm:ss').local().format("YYYY-MM-DD HH:mm:ss");
        },
        createUTCDatetime(datetime) {
            return (
              this.moment(datetime).utc().format("YYYY-MM-DDTHH:mm:SS") + ".000000Z"
            )
        },
        createUTCDateTimeAndAdd(datetime, val=0, unit='h')
        {
            return (
                this.moment(datetime).utc().add(val, unit).format("YYYY-MM-DDTHH:mm:SS") + ".000000Z"
              )
        },
        isDateAfter(firstdate, seconddate)
        {
            return this.moment(firstdate).isValid() && this.moment(seconddate).isValid() ? 
            this.moment(seconddate).isAfter(firstdate) ||  this.moment(seconddate).isSame(firstdate)  : false
        },
        dateFieldFormatter(today){
            return moment(today).format('DD.MM.YYYY')
        }
    }
}
