<template>
  <article id="orderschatstable">
    <b-table
    per-page="25"
      class="btable"
      :data="logs.data"
      bordered
      narrowed
      :total="logs.total"
      :loading="loading"
      paginated
      backend-pagination
      backend-sorting
      @page-change="onPageChange"
    >
      <template #empty>
        
          <b-message type="is-warning" has-icon v-if="!loading">
              No data here yet.
          </b-message>
           <b-message type="is-info" has-icon v-if="loading">
              Loading data...
          </b-message>
        </template>
      <!-- <template #detail="props">
        <article style="text-align: left">
          <StatsDetails :row="props.row" :filters="filters" />
        </article>
      </template> -->
       <b-table-column field="date" label="Date" v-slot="props" width="200">
        {{ parseDateTimeFromUTCtoLocal(props.row.created_at) }}
      </b-table-column>
      <b-table-column
        field="doer"
        label="Agent"
        v-slot="props"
        width="200"
      >
      <b-tag type="is-info"> {{ props.row.doer.firstname}} {{ props.row.doer.lastname}}</b-tag>
      </b-table-column>
       <b-table-column
        field="desc"
        label="Description"
        v-slot="props"
        cell-class="desccolumn"
      >
       {{ props.row.desc }}
      </b-table-column>
       <b-table-column
        field="relateditem"
        label="Item"
        v-slot="props"
      >
       <b-tag v-if="props.row.relateditem.threadid" type="is-link">{{ props.row.relateditem.threadid }}</b-tag>
      </b-table-column>

     
    </b-table>
  </article>
</template>
<style >
.desccolumn
{
  text-align:left !important;
}
</style>
<style>

.stats-sticky-column {
  background: #ffebb8 !important;
  color: rgb(0, 0, 0) !important;
}
.btable {
  font-size: 13px;
}
.btable th {
  font-size: 14px;
  color: rgb(139, 140, 145) !important;
  text-transform: uppercase;
  text-align: center !important;
}

</style>
<script>
import { mapActions, mapState } from "vuex";
import { dateMixin } from "@/mixins/dateMixin.js";
//import tableHelper from "../mixins/tableHelper";
import notificationsMixin from "@/mixins/notificationsMixin";
import memberMixin from "@/mixins/memberMixin";
import tableHelper from "../mixins/tableHelper";
export default {
  name: "SystemLogs",
  mixins: [dateMixin, notificationsMixin, memberMixin, tableHelper],
  components: {},
  methods: {
    ...mapActions("systemlogs", ["loadLogs"]),
    onPageChange(page) {
      this.$store.commit("systemlogs/setPage", page);
      this.loadLogs();
    },
  },
  watch: {},
  mounted() {
    // this.loadPendingChats();
    this.loadLogs().catch((e) => {
      this.notifyWarning(e);
    });
  },
  computed: {
    //...mapState("chat", ["pendingchats", "pendingChatsLoading"]),
    ...mapState("systemlogs", ["logs", "loading"]),
  },
  data() {
    return {};
  },
};
</script>
