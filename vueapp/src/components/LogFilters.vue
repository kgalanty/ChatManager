<template>
  <div class="tile mainfilters">
    <div class="tile is-3 is-child">
      <b-field label="Search" style="width: 95%">
        <b-input
          v-model="searchtext"
          placeholder="Search for item ID"
          @input="doSearch"
          type="search"
          icon="magnify"
        >
        </b-input>
      </b-field>
    </div>
   
    <div class="tile is-3 is-child">
      <b-field label="Operator" style="width: 95%">
        <b-select
          placeholder="Select an operator"
          :loading="loadingOperator"
          @focus="loadOperators"
          v-model="operator"
          expanded
        >
          <option value="">-All-</option>
          <option :value="op.id" :key="i" v-for="(op, i) in operators">
            {{ op.firstname }} {{ op.lastname }}
          </option>
        </b-select>
      </b-field>
    </div>
    <div class="tile is-3 is-child">
      <b-field label="Date [From]" style="width: 95%">
        <b-datepicker
          :max-date="dateTo"
          v-model="dateFrom"
          rounded
          :first-day-of-week="1"
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateFrom = null"
          :date-formatter="dateFieldFormatter"
        >
        </b-datepicker>
      </b-field>
    </div>
    <div class="tile is-3 is-child">
      <b-field label="Date [to]" style="width: 95%">
        <b-datepicker
          :min-date="dateFrom"
          v-model="dateTo"
          rounded
         position="is-bottom-left"
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateTo = null"
          :date-formatter="dateFieldFormatter"
          :first-day-of-week="1"
        >
        </b-datepicker>
      </b-field>
    </div>
  </div>
</template>
<style>
.mainfilters {
  font-size: 1.8rem !important;
}
.dropdown-content > a {
  text-align: left !important;
}
</style>
<style scoped>
.mainfilters {
  font-size: 2.8rem;
}

.tile {
  margin-bottom: 10px;
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";
import { dateMixin } from "../mixins/dateMixin.js";
import notificationsMixin from "../mixins/notificationsMixin.js";
import requestsMixin from "../mixins/requestsMixin.js";
import errorMixin from "../mixins/errorsMixin";
export default {
  name: "LogFilters",
  mixins: [ requestsMixin, dateMixin, notificationsMixin, errorMixin],
  components: {
    //HelloWorld
  },
  methods: {
     ...mapActions("systemlogs", ["loadLogs", 'setOperatorFilter']),
    doSearch() {
      this.$store.commit("systemlogs/setQuery", this.searchtext);

      this.loadLogs().catch((e) => {
        this.showError(e);
      });
    },

   
    loadOperators() {
      return new Promise((resolve) => {
        if (this.operators.length == 0) {
          this.loadingOperator = true;
          const params = this.generateParamsForRequest("Agents");
          this.$api
            .get(`addonmodules.php?${params}&a=GetAgentsList&q=`)
            .then(({ data }) => {
              this.operators = data.data;
            })
            .catch((error) => {
              this.operators = [];
              throw error;
            })
            .finally(() => {
              resolve();
              this.loadingOperator = false;
            });
        }
        resolve();
      });
    },
   ...mapActions("systemlogs", ["loadLogs"]),
    clearField(field) {
      this[field] = null;
      this.$store.commit("systemlogs/setFilter", { [field]: null });
    },

    createUTCDatetime(datetime) {
      return (
        this.moment(datetime).utc().format("YYYY-MM-DDTHH:mm:SS") + ".000000Z"
      );
    },
    parseDateTime(dateTime) {
      return this.moment(dateTime).format("YYYY-MM-DD HH:DD:SS");
    },
    subOneDay(date)
    {
        return this.moment(date).subtract(1, 'd').format("YYYY-MM-DD")
    }
  },

  computed: {
    ...mapState("systemlogs", ["filters"]),

    dateFrom: {
      get() {
        return this.$store.state.systemlogs.filters.dateFrom
          ? new Date(this.$store.state.systemlogs.filters.dateFrom)
          : null;
      },
      set(v) {
        let datefromparsed = v !== null ? this.createUTCDatetime(v) : null;
        this.$store.commit("systemlogs/setFilter", { dateFrom: datefromparsed });
        this.loadLogs()
      },
    },
    dateTo: {
      get() {
        return this.$store.state.systemlogs.filters.dateTo
          ? new Date(this.subOneDay(this.$store.state.systemlogs.filters.dateTo))
          : null;
      },
      set(v) {
        let datefromparsed =
          v !== null ? this.createUTCDateTimeAndAdd(v, 24, "h") : null;
        this.$store.commit("systemlogs/setFilter", { dateTo: datefromparsed });
        this.loadLogs()
      },
    },
  },
    mounted() {
    if (this.filters.dateFrom) {
      this.dateFrom = new Date(this.filters.dateFrom);
    } 
    if (this.filters.dateTo) {
      this.dateTo = new Date(this.filters.dateTo);
    }
    if (this.filters.operator) {
      this.loadOperators().then(() => {
        this.operator = this.filters.operator;
      });
    }
  },
  data() {
    return {
      loadingOperator: false,
      operators: [],
      operator: "",
      searchtext: "",
    };
  },
  watch: {
    operator(val) {
     this.setOperatorFilter(val);
     this.loadLogs()
    },
  },
};
</script>
