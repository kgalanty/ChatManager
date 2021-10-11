import { mapActions } from "vuex";
export const tagsMixin = {
  methods:
  {
    ...mapActions({
      loadTags: "tags/loadTags",
    }),
  },
  mounted() {
      this.loadTags()

  },
  data() {
    return {
      // tagsList: [
      //   "sales",
      //   "wcb",
      //   "directsale",
      //   "convertedsale",
      //   "notsure",
      //   "georgistatev",
      //   "upgrade",
      //   "upsell",
      //   "firstlastname",
      //   "elvira",
      //   "ivaylo",
      //   "domain",
      //   "pushupsell",
      //   "tegan",
      //   "cycle",
      //   "promocode",
      //   "emiliy",
      //   "deni",
      //   "alexp",
      //   "pushcycle",
      //   "pending",
      //   "#blackfriday",
      //   "custom",
      //   "phone",
      //   "#4thjuly",
      //   "duplicate",
      //   "vps/ds",
      //   "cannot offer",
      // ],
    };
  },
}
