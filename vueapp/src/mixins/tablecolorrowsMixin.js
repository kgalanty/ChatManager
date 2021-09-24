
export default {
    methods: {
        colorRows(row) {
            if (this.colorLatefollowup(row)) return "is-latefollowup";
            if (this.colorDirectsale(row.tags)) return "is-directsale";
            if (this.colorCannotoffer(row.tags)) return "is-cannotoffer";
            if (this.colorDuplicate(row.tags)) return "is-duplicate";
          },
          colorDuplicate(tags) {
            return tags.find((e) => {
              if (e.tag == "duplicate" && e.approved == 1) {
                return true;
              }
            });
          },
          colorDirectsale(tags) {
            return tags.find((e) => {
              if (e.tag == "directsale" && e.approved == 1) {
                return true;
              }
            });
          },
          colorCannotoffer(tags) {
            return tags.find((e) => {
              if (e.tag == "cannot offer" && e.approved == 1) {
                return true;
              }
            });
         },
         colorLatefollowup(row) {
            const iswcb = row.tags.find((e) => {
                if (e.tag == "wcb" && e.approved == 1) {
                  return true;
                }
              });
              const now = this.moment().utc()
              if(
                  (iswcb && row?.followup?.length === 0 && this.moment(row.created_at).add(48,'hours').isBefore(now)) ||
                  (iswcb && row?.followup?.length === 1 && this.moment(row.followup[0].followupdate).add(72,'hours').isBefore(now)))

              {
                    return 'is-latefollowup'
              }
            //  if(row?.followup.length === 0)
            //  {

            //  }
            // const now = this.moment().utc()
            // console.log(this.moment(row.created_at).add(48,'hours'))
            // console.log(this.moment(row.created_at).add(48,'hours').isBefore(now))
           // const createdat = this.moment(row.created_at).add(48,'hours')

         //   console.log(this.moment().utc().format("YYYY-MM-DD HH:DD:SS"));
         },
    }
}
