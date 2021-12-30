<template>
  <form action="">
    <div class="modal-card" style="width: 95vw">
      <header class="modal-card-head">
        <p class="modal-card-title">External Points Review - {{agent.agent_name}} {{ dateFromUTC }} - {{ dateToUTC}}</p>
        <button type="button" class="delete" @click="$emit('close')" />
      </header>
      <section class="modal-card-body">
        <!-- <b-message title="Info" type="is-info" has-icon :closable="false">
          This form can be used to manually add (or remove) points for certain
          agent. To make it correct, fill number of points (if negative, add `-`
          before number, example: -2), date on which it will be counted and
          comment with reason for this operation.
        </b-message> -->

        <b-table
          class="btable statstable"
          :data="data.rows"
          :total="data.total"
          paginated
          striped
          backend-pagination
          backend-sorting
          @page-change="onPageChange"
          :per-page="perpage"
          :loading="loading.tableloading"
          pagination-position="both"
          scrollable
        >
          <template #empty style="text-align: center">
            <span v-if="loading.tableloading === false">
              <p>No entries for given criteria.</p>
            </span>
            <b-message type="is-info" has-icon v-if="loading.tableloading">
              Loading data...
            </b-message>
          </template>
          <b-table-column field="date" label="ID" width="40" v-slot="props">
            {{ props.row.id}}
          </b-table-column>
          <b-table-column field="date" label="Date Applicable" width="120" v-slot="props">
            {{ props.row.date}}
          </b-table-column>
          <b-table-column field="date" label="Entry Author" width="250" v-slot="props">
            {{ props.row.author.firstname}} {{ props.row.author.lastname}}
          </b-table-column>
          <b-table-column field="date" label="Points" width="100" v-slot="props">
             <b-tag :type="props.row.points > 0 ? 'is-success' : 'is-danger'">{{ props.row.points}}</b-tag>
          </b-table-column>
          <b-table-column field="date" label="Comment" v-slot="props">
            {{ props.row.comment }}
          </b-table-column>
          <b-table-column field="date" label="Date added" width="250" v-slot="props">
            {{ props.row.created_at }}
          </b-table-column>
        </b-table>
      </section>
      <footer class="modal-card-foot">
        <b-button label="Close" @click="$emit('close')" />
      </footer>
    </div>
  </form>
</template>
<script>
import { mapActions } from "vuex";
import { dateMixin } from "../mixins/dateMixin.js";
import memberMixin from "../mixins/memberMixin";
import requestMixin from "../mixins/requestsMixin";
import notificationsMixin from "../mixins/notificationsMixin";
import errorsMixin from "../../../chat-nuxt/mixins/errorsMixin.js";

export default {
  name: "ShowManualPointsModal",
  mixins: [
    dateMixin,
    memberMixin,
    notificationsMixin,
    requestMixin,
    errorsMixin,
  ],
  props: {
    agent: {
      type: Object,
    },
    dates: {
      type: Object,
    },
  },
  components: {},
  computed: {
    dateFromUTC()
    {
      return this.parseDate(this.dates.datefrom)
    },
    dateToUTC()
    {
      return this.parseDate(this.dates.dateto)
    },
  },
  methods: {
    ...mapActions({
      getPermissions: "getPermissions",
      loadOperators: "operators/loadOps",
    }),
    onPageChange(page) {
      this.page = page;
      this.getdata();
    },
    getdata() {
      // if (this.points == 0 || !this.points) {
      //   this.notifyDanger("You need to set points.");
      //   return;
      // }
      // if (!(this.agentSelected && this.agentSelected.id)) {
      //   this.notifyDanger("You need to set operator.");
      //   return;
      // }
      // this.loading.saveLoadingBtn = true;
    let dateFromUTC = this.createUTCDatetime(this.dates.datefrom)
    let dateToUTC = this.createUTCDatetime(this.dates.dateto)
      const params = this.generateParamsForRequest("Points", [
        "a=GetSingleAgentPoints",
        `agentid=${this.agent.agent_id}`,
        `datefrom=${dateFromUTC}`,
        `dateto=${dateToUTC}`,
        `page=${this.page}`,
        `perpage=${this.perpage}`
      ]);
      this.loading.tableloading = true;

      this.$api
        .get(`addonmodules.php?${params}`)
        .then((response) => {
          if (response.data.result == "success") {
            this.data.total = response.data.total
            this.data.rows = response.data.data
          } else {
            this.showError(response);
          }
        })
        .catch((e) => {
          this.showError(e);
        })
        .finally(() => {
          this.loading.tableloading = false;
        });
    },
  },
  mounted() {
    //console.log(this.dates)
    this.getdata();
  },

  data() {
    return {
      page: 1,
      perpage: 25,
      data: { total: 0, rows: [] },
      loading: {
        tableloading: false,
      },
    };
  },
};
</script>
<style>
</style>
