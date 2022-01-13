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
    };
  },
}
