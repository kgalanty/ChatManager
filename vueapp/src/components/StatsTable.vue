<template>
  <article id="statstable">
    <div class="tile statsfilters">
      <div class="tile is-2 is-child" v-if="isAdmin()">
        <b-field label="Operator" style="width: 95%; padding: 9px">
          <b-select
            placeholder="Select an operator"
            :loading="loading.operators"
            @focus="loadOperators"
            v-model="filters.operator"
            expanded
            @input="loadStats"
          >
            <option value="">-All-</option>
            <option :value="op.id" :key="i" v-for="(op, i) in operators">
              {{ op.firstname }} {{ op.lastname }}
            </option>
          </b-select>
        </b-field>
      </div>
      <div class="tile is-2 is-child">
        <b-field label="Date [From]" style="width: 95%; padding: 9px">
          <b-datepicker
            :max-date="filters.dateTo"
            v-model="filters.dateFrom"
            placeholder="Click to select..."
            icon="calendar-today"
            @input="loadStats"
            :date-formatter="dateFieldFormatter"
          >
          </b-datepicker>
        </b-field>
      </div>
      <div class="tile is-2 is-child">
        <b-field label="Date [to]" style="width: 95%; padding: 9px">
          <b-datepicker
            :min-date="filters.dateFrom"
            v-model="filters.dateTo"
            placeholder="Click to select..."
            icon="calendar-today"
            @input="loadStats"
            :date-formatter="dateFieldFormatter"
          >
          </b-datepicker>
        </b-field>
      </div>
      <div class="tile is-1 is-child">
        <b-field label="Save as PDF" style="width: 95%; padding: 9px">
          <b-button type="is-primary" @click="openExport" expanded
            >Download</b-button
          >
        </b-field>
      </div>
      <div class="tile is-1 is-child" v-if="isAdmin()">
        <b-field label="Add Points" style="width: 95%; padding: 9px">
          <b-button type="is-primary" @click="openAddPointsModal" expanded
            >Add</b-button
          >
        </b-field>
      </div>
    </div>
    <!-- <img alt="Vue logo" src="../assets/logo.png"> -->
    <b-table
      class="btable statstable"
      :data="stats.data"
      bordered
      :row-class="colorSum"
      narrowed
      :total="stats.total"
      :loading="loading.stats"
      pagination-position="top"
      detailed
      striped
      detail-transition="fade"
    >
      <template #empty>
        <div>
          <b-message
            type="is-warning"
            has-icon
            v-if="loading && !loading.stats"
          >
            No results for given criteria.
          </b-message>
          <b-message type="is-info" has-icon v-if="loading && loading.stats">
            Loading data...
          </b-message>
        </div>
      </template>

      <template #detail="props">
        <article style="text-align: left" v-if="props.row.data.agent_id">
          <StatsDetails :row="props.row" :filters="filters" />
        </article>
      </template>
      <b-table-column
        field="date"
        label="Agent"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.data.agent_name }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Can Offer"
        v-slot="props"
        width="100"
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
        centered
      >
        {{ props.row.directsale + props.row.wcb + props.row.manualpoints }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Cannot Offer"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.cannotoffer }} ({{ cannotofferPercent(props.row) }} %)
      </b-table-column>
      <b-table-column
        field="date"
        label="Total Sales Chats"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.directsale + props.row.wcb + props.row.cannotoffer }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Direct Sales"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.directsale }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Converted Sales"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.convertedsale }}
      </b-table-column>
      <b-table-column
        field="upgrade"
        label="Upgrades"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.upgrade }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Total Sales"
        v-slot="props"
        width="100"
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
        centered
      >
        {{ props.row.directsale + props.row.convertedsale + props.row.upgrade }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Upsell"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.upsell }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Cycle"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.cycle }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Stayed"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row.data.cm_points ? props.row.data.cm_points : 0 }}
      </b-table-column>
      <b-table-column
        field="vps/ds"
        label="VPS/DS"
        v-slot="props"
        width="100"
        centered
      >
        {{ props.row["vps/ds"] ? props.row["vps/ds"] : 0 }}
      </b-table-column>
      <b-table-column
        field="date"
        label="External Points"
        v-slot="props"
        width="100"
        centered
      >
        <a
          v-if="
            props.row.data.agent_name != 'TEAM' &&
           !isNaN(props.row.manualpoints)
          "
          @click="OpenManualPointsModal(props.row.data)"
          ><b-tooltip label="Click for more info" >{{ props.row.manualpoints }}</b-tooltip></a
        >
        <span v-else-if="props.row.data.agent_name == 'TEAM'">{{
          props.row.manualpoints
        }}</span>
        <span v-else>0</span>
      </b-table-column>
      <b-table-column
        field="date"
        label="Total Points"
        v-slot="props"
        width="100"
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
        centered
      >
        {{
          props.row.directsale +
          props.row.convertedsale +
          props.row.upsell +
          props.row.cycle +
          props.row["vps/ds"] -
          props.row.decrementpoints +
          props.row.data.cm_points +
          props.row.upgrade +
          props.row.manualpoints
        }}
      </b-table-column>
      <b-table-column
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
        field="date"
        label="Conversion without points"
        v-slot="props"
        width="100"
        centered
      >
        {{
          props.row.directsale + props.row.wcb + props.row.manualpoints > 0
            ? returnPercents(
                ((props.row.directsale +
                  props.row.convertedsale +
                  props.row.upgrade +
                  props.row.manualpoints) *
                  100) /
                  (props.row.directsale +
                    props.row.wcb +
                    props.row.manualpoints)
              )
            : "0 %"
        }}
      </b-table-column>

      <b-table-column
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
        field="date"
        label="Conversion with points"
        v-slot="props"
        width="100"
        centered
      >
        {{
          props.row.directsale + props.row.wcb > 0
            ? returnPercents(
                ((props.row.directsale +
                  props.row.convertedsale +
                  props.row.upsell +
                  props.row.cycle +
                  props.row.data.cm_points +
                  props.row.upgrade +
                  props.row["vps/ds"]) *
                  100) /
                  (props.row.directsale + props.row.wcb)
              )
            : "0 %"
        }}
      </b-table-column>
    </b-table>
  </article>
</template>
<style>
#statstable .table.is-striped tbody tr:not(.is-selected):nth-child(even) {
  background: #efefef;
}
.stats-sticky-column {
  background: #ffebb8 !important;
  color: rgb(0, 0, 0);
}

.btable {
  font-size: 13px;
}
.btable th {
  font-size: 14px;
  background: rgb(228, 228, 228);
  color: rgb(0, 0, 0) !important;
  text-transform: uppercase;
}
#pendingchatlisttable th span {
  margin: 0 auto;
  text-align: center;
}
.is-sum td {
  background: rgb(79, 63, 107) !important;
  color: white !important;
  z-index: 3;
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
//import { mapActions, mapState } from "vuex";
import "buefy/dist/buefy.css";
import { dateMixin } from "@/mixins/dateMixin.js";
//import tableHelper from "../mixins/tableHelper";
import axios from "axios";
import requestsMixin from "../mixins/requestsMixin";
import StatsDetails from "@/components/StatsDetails";
import notificationsMixin from "../mixins/notificationsMixin";
import memberMixin from "../mixins/memberMixin";
import AddManualPointsModal from "./AddManualPointsModal.vue";
import ShowManualPointsModal from "./ShowManualPointsModal.vue";
import errorsMixin from "../mixins/errorsMixin";
export default {
  name: "StatsTable",
  mixins: [
    dateMixin,
    requestsMixin,
    notificationsMixin,
    memberMixin,
    errorsMixin,
  ],
  components: { StatsDetails },
  methods: {
    OpenManualPointsModal(agentdata) {
      let dates = {
        datefrom: this.createUTCDatetime(this.filters.dateFrom),
        dateto: this.createUTCDatetime(this.filters.dateTo),
      };
      this.$buefy.modal.open({
        parent: this,
        component: ShowManualPointsModal,
        hasModalCard: true,
        props: { agent: agentdata, dates },
        // customClass: this.darkstyle ? 'darktheme' : 'lighttheme',
        trapFocus: true,
        width: "auto",
      });
    },
    openAddPointsModal() {
    this.$buefy.modal.open({
        parent: this,
        component: AddManualPointsModal,
        hasModalCard: true,
        props: {},
        // customClass: this.darkstyle ? 'darktheme' : 'lighttheme',
        trapFocus: true,
        width: "auto",
        events: {
          refreshstats: () => {
            this.loadStats()
          },
        },
      });
    },
    openExport() {
      let { dateFrom, dateTo } = this.getDateFilters();
      const params = this.generateParamsForRequest("Export", [
        `datefrom=${dateFrom}`,
        `dateto=${dateTo}`,
        `op=${this.filters.operator}`,
        `tz=${Intl.DateTimeFormat().resolvedOptions().timeZone}`,
      ]);
      this.$api
        .get(`addonmodules.php?${params}`, {
          method: "GET",
          responseType: "blob",
        })
        .then((response) => {
          const type = response.headers["content-type"];
          const blob = new Blob([response.data], {
            type: type,
            encoding: "UTF-8",
          });
          const link = document.createElement("a");
          link.href = window.URL.createObjectURL(blob);
          link.download = "report-stats.pdf";
          link.click();
          link.remove();
        })
        .catch((error) => {
          this.showError(error);
        })
        .finally(() => {});
    },
    colorSum(row) {
      //if(this.colorDirectConvertedSaleLackOrder(row)) return 'is-lackorder'
      if (!row.data.agent_id && this.stats.total > 0) return "is-sum";
    },
    // ...mapActions("chat", ["loadPendingChats", "loadChats"]),
    // isEditActive(row) {
    //   row.tags.find((e) => {
    //     if (e.tag == "duplicate" && e.approved == 1) {
    //       return false;
    //     }
    //   });
    //   return true;
    // },\
    //  loadDetails(row) {
    //console.log(row);
    // },
    returnPercents(val) {
      return +parseFloat(val).toFixed(2) + "%";
    },
    cannotofferPercent(row) {
      if (row.directsale + row.wcb + row.cannotoffer == 0) return 0;
      return Math.round(
        (row.cannotoffer / (row.directsale + row.wcb + row.cannotoffer)) * 100
      );
    },
    loadOperators() {
      if (this.operators.length == 0) {
        this.loading.operators = true;
        const params = this.generateParamsForRequest("Agents", [
          "a=GetAgentsList",
          "q=",
        ]);
        this.$api
          .get(`addonmodules.php?${params}`)
          .then(({ data }) => {
            this.operators = data.data;
          })
          .catch((error) => {
            this.operators = [];
            throw error;
          })
          .finally(() => {
            this.loading.operators = false;
          });
      }
    },
    setDateFilters(dateFrom = null, dateTo = null) {
      if (!this.filters.dateFrom && dateFrom) {
        this.filters.dateFrom = new Date(dateFrom);
      }
      if (!this.filters.dateTo && dateTo) {
        this.filters.dateTo = new Date(dateTo);
      }
    },
    getDateFilters() {
      const startDay = this.moment().format("DD") < 16 ? 1 : 16;
      const dateFrom = this.filters.dateFrom
        ? this.createUTCDatetime(this.filters.dateFrom)
        : this.createUTCDatetime(this.moment().format("YYYY-MM-" + startDay));

      const dateTo = this.filters.dateTo
        ? this.createUTCDateTimeAndAdd(this.filters.dateTo, "1", "d")
        : this.createUTCDatetime(
            this.moment().endOf("month").format("YYYY-MM-DD")
          );
      return { dateFrom, dateTo };
    },
    loadStats() {
      this.loading.stats = true;
      // const endDay = this.moment().daysInMonth()
      // console.log(startDay)
      // console.log(endDay)
      //console.log(this.isDateAfter(this.filters.dateFrom, this.filters.dateTo))
      let { dateFrom, dateTo } = this.getDateFilters();
      this.setDateFilters(dateFrom, dateTo);
      const params = this.generateParamsForRequest("Stats", [
        `datefrom=${dateFrom}`,
        `dateto=${dateTo}`,
        `op=${this.filters.operator}`,
        `tz=${Intl.DateTimeFormat().resolvedOptions().timeZone}`,
      ]);

      //context.commit('setChatsPage', payload.chatsPage)
      axios
        .get("addonmodules.php?" + params)
        .then((response) => {
          if (response.data) {
            this.stats.data = response.data.data;
            this.stats.total = response.data.data.length;
          }
        })
        .catch((e) => {
          this.showError(
            "Failed to download data. Check if you are logged in and have permission. Details: " +
              e
          );
          //window.location = 'login.php'
        })
        .finally(() => {
          this.loading.stats = false;
        });
    },
  },
  watch: {
    dateFrom(n, o) {
      if (!this.isDateAfter(n, this.filters.dateTo)) {
        this.filters.dateFrom = new Date(o);
        this.notifyWarning('Date is after "Date To". Restored previous date.');
      }
    },
    dateTo(n, o) {
      if (!this.isDateAfter(this.filters.dateFrom, n)) {
        this.notifyWarning(
          'Date is before "Date From". Restored previous date.'
        );
        this.filters.dateTo = new Date(o);
      }
    },
  },
  mounted() {
    // this.loadPendingChats();
    this.loadStats();
  },
  computed: {
    dateFrom() {
      return this.filters.dateFrom;
    },
    dateTo() {
      return this.filters.dateTo;
    },
    //...mapState("chat", ["pendingchats", "pendingChatsLoading"]),
    //...mapState('chatcolumns', ['filters']),
  },
  data() {
    return {
      loading: {
        stats: false,
        operators: false,
      },
      stats: {
        data: [],
        total: 0,
      },
      filters: {
        dateFrom: null,
        dateTo: null,
        operator: "",
      },
      operators: [],
      Details: [],
    };
  },
};
</script>
