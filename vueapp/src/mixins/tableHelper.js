
export default {
  data() {
    return {
    };
  },
  methods:
  {
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
    calculatePointsFromTags(tags) {
      var n = 0
      for (const i of tags) {
        if (i.tag == 'directsale') n++
        if (i.tag == 'covneredsale') n++
        if (i.tag == 'upsell') n++
        if (i.tag == 'cycle') n++
        if (i.tag == 'vps/ds') n++

      }
      return n > 0 ? n : ''
    }
  }
}
