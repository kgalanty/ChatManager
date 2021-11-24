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
      <b-taglist attached v-for="i in agents" :key="i.id" class="stafftag" ><b-tag 
        >{{ i.agent.firstname }} {{ i.agent.lastname }}</b-tag><b-tag type="is-dark"> {{ moment.utc(i.date).fromNow() }}</b-tag
      ></b-taglist>
    </p>
  </article>
</template>
<style scoped>
.stafftag {
  margin: 5px; 
  display:inline-block;
  margin-bottom: 0 !important;
}
</style>
<script>
import { mapState } from "vuex";
import requestsMixin from "../mixins/requestsMixin";
// @ is an alias to /src
export default {
  name: "StaffOnline",
  // mixins: [tagsMixin, requestsMixin, dateMixin, notificationsMixin, errorMixin],
  mixins: [requestsMixin],
  components: {
    //HelloWorld
  },
  methods: {
    retrieveStaff() {
      const params = this.generateParamsForRequest("StaffOnline");
      this.$api.get(`addonmodules.php?${params}`).then((data) => {
        if (data.data.result === "success") {
          this.agents = data.data.data;
        }
      });
    },
  },
  beforeDestroy() {
    this.$store.commit("staffonline/setidentifier", null);
  },
  mounted() {
    this.retrieveStaff()
    if (!this.intervalIdentifier) {
      let identifier = setInterval(this.retrieveStaff, 30000);
      this.$store.commit("staffonline/setidentifier", identifier);
    }
    // this.retrieveStaff()
  },
  computed: {
    ...mapState("staffonline", ["intervalIdentifier"]),
  },
  data() {
    return {
      agents: [],
    };
  },
};
</script>
