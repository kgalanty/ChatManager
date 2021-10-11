<template>
  <span v-if="showfollowup(row)">
    <b-button
      type="is-primary"
      @click="click"
      v-if="row.followup && row.followup.length < 2"
      :disabled="disabled"
      :loading="loading"
      size="is-small"
      >Confirm</b-button
    ><b-field>
      <b-tag
        type="is-info"
        style="font-size: 14px"
        v-if="row.followup && row.followup.length == 1"
      >
        <b-icon icon="alarm" size="is-small"></b-icon>
        &nbsp;&nbsp;{{ diff }}
      </b-tag>

      <b-icon
        v-if="row.followup && row.followup.length >= 2"
        icon="check"
        type="is-success"
      ></b-icon>
    </b-field>
  </span>
</template>
<style>
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions } from "vuex";
import tableHelper from "../mixins/tableHelper";
export default {
  name: "TableFollowUp",
  components: {},
  mixins: [tableHelper],
  props: ["row", "afterClickAction"],
  methods: {
    ...mapActions({
      loadChats: "chat/loadChats",
      loadPendingChats: "chat/loadPendingChats",
      getPermissions: "getPermissions",
    }),
    click() {
      this.loading = true;
      this.disabled = true;
      this.followup(this.row).then(() => {
        {
          this.loading = false;
          this.disabled = false;
          if (this.afterClickAction) {
            this[this.afterClickAction]();
          }
        }
      });
    },
    updateTimers() {
      var that = this;
      this.interval = setInterval(() => {
        if (this.row.followup.length == 1) {
          that.diff = that.calcFollowUp(that.row);
        } else {
          if (this.interval) {
            clearInterval(this.interval);
            this.interval = null
          }
        }
      }, 60000);
    },
  },
  updated() {
    this.updateTimers();
  },
  mounted() {
    this.diff = this.calcFollowUp(this.row);
  },
  computed: {},
  data() {
    return {
      disabled: false,
      loading: false,
      diff: "",
      interval: null,
    };
  },
};
</script>
