<template>
  <article id="statstable">
    <div class="tile statsfilters">
      <div class="tile is-2 is-child">
        <b-field label="Operator" style="width: 95%; padding: 9px" v-if="isAdmin()">
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
            :icon-right="'close-circle'"
            icon-right-clickable
            @icon-right-click="filters.dateFrom = null"
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
            :icon-right="'close-circle'"
            icon-right-clickable
            @input="loadStats"
            @icon-right-click="filters.dateTo = null"
            :date-formatter="dateFieldFormatter"
           
          >
          </b-datepicker>
        </b-field>
      </div>
    </div>
    <!-- <img alt="Vue logo" src="../assets/logo.png"> -->
    <b-table
      class="btable"
      :data="stats.data"
      bordered
       :row-class="colorSum"
      narrowed
      :total="stats.total"
      :loading="this.loading.stats"
      pagination-position="top"
      detailed
      detail-transition="fade"
    >
      <template #empty >
        <div  style="background: white !important; color:black;">
        No entries yet.</div>
        </template>
      <template #detail="props" >
        <article style="text-align: left" v-if="props.row.data.agent_id">
          <StatsDetails :row="props.row" :filters="filters" />
        </article>
      </template>
      <b-table-column field="date" label="Agent" v-slot="props" width="100">
        {{ props.row.data.agent_name }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Can Offer"
        v-slot="props"
        width="100"
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
      >
        {{ props.row.directsale + props.row.wcb }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Cannot Offer"
        v-slot="props"
        width="100"
      >
        {{ props.row.cannotoffer }} ({{ cannotofferPercent(props.row) }} %)
      </b-table-column>
      <b-table-column
        field="date"
        label="Total Sales Chats"
        v-slot="props"
        width="100"
      >
        {{ props.row.directsale + props.row.wcb + props.row.cannotoffer }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Direct Sales"
        v-slot="props"
        width="100"
      >
        {{ props.row.directsale }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Converted Sales"
        v-slot="props"
        width="100"
      >
        {{ props.row.convertedsale }}
      </b-table-column>
      <b-table-column
        field="uupgrade"
        label="Upgrades"
        v-slot="props"
        width="100"
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
      >
        {{ props.row.directsale + props.row.convertedsale + props.row.upgrade }}
      </b-table-column>
      <b-table-column field="date" label="Upsell" v-slot="props" width="100">
        {{ props.row.upsell }}
      </b-table-column>
      <b-table-column field="date" label="Cycle" v-slot="props" width="100">
        {{ props.row.cycle }}
      </b-table-column>
      <b-table-column field="date" label="Stayed" v-slot="props" width="100">
        {{ props.row.data.cm_points ? props.row.data.cm_points : 0 }}
      </b-table-column>
      <b-table-column field="vps/ds" label="VPS/DS" v-slot="props" width="100">
        {{ props.row["vps/ds"] ? props.row["vps/ds"] : 0 }}
      </b-table-column>
     <b-table-column
        field="date"
        label="Total Points"
        v-slot="props"
        width="100"
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
      >
        {{
          props.row.directsale +
          props.row.convertedsale +
          props.row.upsell +
          props.row.cycle +
          props.row["vps/ds"] -
          props.row.decrementpoints +
          props.row.data.cm_points +
          props.row.upgrade
        }}
      </b-table-column>
      <b-table-column
        header-class="stats-sticky-column"
        cell-class="stats-sticky-column"
        field="date"
        label="Conversion without points"
        v-slot="props"
        width="100"
      >
        {{
          props.row.directsale + props.row.wcb > 0
            ? returnPercents(
                ((props.row.directsale + props.row.convertedsale + props.row.upgrade) * 100) /
                  (props.row.directsale + props.row.wcb)
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
.stats-sticky-column {
  background: #ffebb8;
  color: rgb(0, 0, 0) ;
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
#pendingchatlisttable th span {
  margin: 0 auto;
  text-align: center;
}
.is-sum td
{
  background:rgb(79, 63, 107) !important;
  color:white !important;
  z-index:3;
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
import notificationsMixin from '../mixins/notificationsMixin';
import memberMixin from '../mixins/memberMixin';
export default {
  name: "StatsTable",
  mixins: [dateMixin, requestsMixin, notificationsMixin, memberMixin],
  components: { StatsDetails },
  methods: {
    colorSum(row) {
      //if(this.colorDirectConvertedSaleLackOrder(row)) return 'is-lackorder'
      if(!row.data.agent_id && this.stats.total > 0) return 'is-sum'
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
         const params = this.generateParamsForRequest("Agents", ['a=GetAgentsList', 'q=']);
        this.$api
          .get(
           `addonmodules.php?${params}`
          )
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
    loadStats() {
      const startDay = this.moment().format("DD") < 16 ? 1 : 16;
      this.loading.stats = true;
      // const endDay = this.moment().daysInMonth()
      // console.log(startDay)
      // console.log(endDay)
      //console.log(this.isDateAfter(this.filters.dateFrom, this.filters.dateTo))
      const dateFrom = this.filters.dateFrom
        ? this.createUTCDatetime(this.filters.dateFrom)
        : this.createUTCDatetime(this.moment().format("YYYY-MM-" + startDay));

      const dateTo = this.filters.dateTo
        ? this.createUTCDateTimeAndAdd(this.filters.dateTo, "1", "d")
        : this.createUTCDatetime(
            this.moment().endOf('month').format("YYYY-MM-DD")
          );
      this.setDateFilters(dateFrom, dateTo);
      const params = this.generateParamsForRequest("Stats", [
        `datefrom=${dateFrom}`,
        `dateto=${dateTo}`,
        `op=${this.filters.operator}`,
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
          console.log(e);
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
        this.notifyWarning('Date is after "Date To". Restored previous date.')
      }
    },
    dateTo(n, o) {
      if (!this.isDateAfter(this.filters.dateFrom,n)) {
        this.notifyWarning('Date is before "Date From". Restored previous date.')
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
