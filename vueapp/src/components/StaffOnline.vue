<template>
  <article
    style="
      padding: 2px;
      background: rgb(66 65 118);
      color: #c1bbe3;
      padding: 5px;
      border-radius: 5px;
    "
  >
    <span class="is-family-sans-serif"> Staff seen recently </span>
    <p>
      <b-taglist attached v-for="i in agents" :key="i.id" class="stafftag"
        ><b-tag>{{ i.agent.firstname }} {{ i.agent.lastname }}</b-tag
        ><b-tag type="is-dark">
          {{
            DateFromNow(i.date) == "a few seconds ago"
              ? "Now"
              : DateFromNow(i.date)
          }}</b-tag
        ></b-taglist
      >
    </p>
  </article>
</template>
<style scoped>
.stafftag {
  margin: 5px;
  display: inline-block;
  margin-bottom: 0 !important;
}
</style>
<script>
import { mapState } from "vuex";
import requestsMixin from "../mixins/requestsMixin";
import versionMixin from "@/mixins/versionMixin"; 
//const appver = '10.0.12'
// @ is an alias to /src
export default {
  name: "StaffOnline",
  // mixins: [tagsMixin, requestsMixin, dateMixin, notificationsMixin, errorMixin],
  mixins: [requestsMixin, versionMixin],
  components: {
    //HelloWorld
  },
  methods: {
    DateFromNow(date) {
      return this.moment.utc(date).fromNow();
    },
    retrieveStaff() {
      const params = this.generateParamsForRequest("StaffOnline");
      this.$api.get(`addonmodules.php?${params}`).then((data) => {
        if (data.data.result === "success") {
          this.agents = data.data.data;
          if(data.data.appver && this.verifyVer(data.data.appver) && !this.versionMsgShown)
          {
            this.appVerInconsistencyWarn()
            this.versionMsgShown = true
          }

        }
      });
    },
  },
  beforeDestroy() {
    this.$store.commit("staffonline/setidentifier", null);
  },
  mounted() {
    this.retrieveStaff();
    if (!this.intervalIdentifier) {
      let identifier = setInterval(this.retrieveStaff, 30000);
      this.$store.commit("staffonline/setidentifier", identifier);
    }
  },
  computed: {
    ...mapState("staffonline", ["intervalIdentifier"]),
  },
  data() {
    return {
      agents: [],
      versionMsgShown: false,
    };
  },
};
</script>
