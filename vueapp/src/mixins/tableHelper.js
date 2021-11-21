
export default {
  data() {
    return {
    };
  },
  methods:
  {
    operator(email) {
      if (email) {
        const capitalize = ([first, ...rest]) =>
          first.toUpperCase() + rest.join("").toLowerCase();
        let firstlastname = email.split("@")[0].split(".");
        if (firstlastname.length < 2) return email;
        firstlastname[0].charAt(0).toUpperCase() +
          firstlastname[0].slice(1).toLowerCase();
        firstlastname[1].charAt(0).toUpperCase() +
          firstlastname[1].slice(1).toLowerCase();

        return (
          capitalize(firstlastname[0]) + " " + capitalize(firstlastname[1])
        );
      }
      return email;
    },
    showAllChats(customerid) {
      window.open("https://my.livechatinc.com/archives/?query=" + customerid);
      //https://my.livechatinc.com/archives/?query=93380b5f-2561-4286-76dd-57a457fe8b5b
    },
    showfollowup(row) {
      return row.tags.find((e) => {
        if (e.tag == "wcb" && e.approved == 1 && row.orderid == null) {
          return true;
        }
      });
    },
    calcFollowUp(row) {
      if (row.followup.length > 0) {
        var now = this.moment.utc(
          row.followup[row.followup.length - 1].followupdate
        );
        // var dur = this.moment(now).utc().fromNow()
        // var duration = this.moment.duration(now.diff(end))
        var result = this.moment.utc(now).fromNow();
        return result;
      }
      return "None";
    },
    followup(row) {
      const params = [`module=ChatManager`, `c=FollowUp`, `json=1`].join("&");
      return new Promise((resolve) => {
        this.$api
          .post(`addonmodules.php?${params}`, {
            threadid: row.id,
          })
          .then((response) => {
            if (response.data.result == "success") {
              this.$buefy.toast.open({
                container: ".modal-card",
                message: "Entry marked",
                type: "is-success",
              });

            } else {
              this.$buefy.toast.open({
                container: ".modal-card",
                message: response.data.result,
                type: "is-warning",
              });
            }
            resolve()
          });
      })
    },
    calculatePointsFromTags(tags, invoiceStatus, invoice) {
      let n = 0
      var upgradeMarker = false
      if(invoiceStatus != 'Paid' && !invoice) return ''
      for (let i of tags) {
        if(parseInt(i.approved) !== 1) continue
       
        if (i.tag == "directsale") n++
        if (i.tag == "convertedsale") n++
        if (i.tag == "upsell") n++
        if (i.tag == "cycle") n++
        if (i.tag == "vps/ds") n++
        if (i.tag == "upgrade") upgradeMarker = true
      }
      if(upgradeMarker && invoice.status == 'Paid') return 1
      return n > 0 ? n : ''
    }
  }
}
