<template>
  <div class="tile mainfilters">
    <div class="tile is-2 is-child">
      <b-field label="Search" style="width: 95%">
        <b-input
          v-model="searchtext"
          placeholder="Search for chat ID/E-mail/Domain/Order ID"
          @input="doSearch"
          type="search"
          icon="magnify"
        >
        </b-input>
      </b-field>
    </div>
    <div class="tile is-2 is-child">
      <b-field label="Tags" style="width: 95%">
        <b-taginput
          v-model="tags"
          :data="filteredTags"
          autocomplete
          field="tag.tag"
          icon="label"
          type="is-info"
          placeholder="Start typing"
          @typing="getFilteredTags"
         
          :open-on-focus="true"
        >
        </b-taginput>
      </b-field>
    </div>
    <div class="tile is-2 is-child">
      <b-field label="Exclude Tags" style="width: 95%">
        <b-taginput
          v-model="extags"
          :data="filteredTags"
          autocomplete
          field="tag.tag"
          icon="label"
          type="is-info"
          placeholder="Start typing"
          @typing="getFilteredTags"
         
          :open-on-focus="true"
        >
        </b-taginput>
      </b-field>
    </div>
    <div class="tile is-2 is-child">
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
    <div class="tile is-2 is-child">
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
    <div class="tile is-2 is-child">
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
import { tagsMixin } from "../mixins/tagsMixin.js";
import errorMixin from "../mixins/errorsMixin";
export default {
  name: "ChatFilters",
  mixins: [tagsMixin, requestsMixin, dateMixin, notificationsMixin, errorMixin],
  components: {
    //HelloWorld
  },
  methods: {
    doSearch() {
      this.$store.commit("chat/setQuery", this.searchtext);

      this.loadChats().catch((e) => {
        this.showError(e);
      });
    },

    getFilteredTags(text) {
      this.filteredTags = this.$store.state.tags.tags.filter((option) => {
        return option.toString().toLowerCase().indexOf(text.toLowerCase()) >= 0;
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
    ...mapActions({
      loadChats: "chat/loadChats",
      setTagsFilter: "chat/setTagsFilter",
      setExTagsFilter: 'chat/setExTagsFilter',
      setOperatorFilter: "chat/setOperatorFilter",
    }),
    clearField(field) {
      this[field] = null;
      this.$store.commit("chat/setFilter", { [field]: null });
    },
    constructParams() {
      const params = {
        perPage: this.perPage,
        Datefrom: this.dateFrom,
        Dateto: this.dateTo,
      };
      return params;
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
  mounted() {
    if (this.filters.tags) {
      this.tags = this.filters.tags;
    }
    if (this.filters.dateFrom) {
      this.dateFrom = new Date(this.filters.dateFrom);
    } else {
      // let dayToday = this.moment().utc().format('DD')
      // if(dayToday < 16)
      //{
      let dateGeneral = new Date();
      this.dateFrom = new Date(
        dateGeneral.getUTCFullYear(),
        dateGeneral.getUTCMonth(),
        dateGeneral.getUTCDate()
      );
      //}
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
  computed: {
    ...mapState("chat", ["filters"]),
    tags: {
      get() {
        return this.$store.state.chat.filters.tags
          ? this.$store.state.chat.filters.tags
          : [];
      },
      set(v) {
        this.setTagsFilter(v);
        this.loadChats().catch((e) => {
          this.showError(e);
        });
      },
    },
      extags: {
      get() {
        return this.$store.state.chat.filters.extags
          ? this.$store.state.chat.filters.extags
          : [];
      },
      set(v) {
        this.setExTagsFilter(v);
        this.loadChats().catch((e) => {
          this.showError(e);
        });
      },
    },
    dateFrom: {
      get() {
        return this.$store.state.chat.filters.dateFrom
          ? new Date(this.$store.state.chat.filters.dateFrom)
          : null;
      },
      set(v) {
        let datefromparsed = v !== null ? this.createUTCDatetime(v) : null;
        this.$store.commit("chat/setFilter", { dateFrom: datefromparsed });
        this.loadChats().catch((e) => {
          this.showError(e);
        });
      },
    },
    dateTo: {
      get() {
        return this.$store.state.chat.filters.dateTo
          ? new Date(this.subOneDay(this.$store.state.chat.filters.dateTo))
          : null;
      },
      set(v) {
        let datefromparsed =
          v !== null ? this.createUTCDateTimeAndAdd(v, 24, "h") : null;
        this.$store.commit("chat/setFilter", { dateTo: datefromparsed });
        this.loadChats().catch((e) => {
          this.showError(e);
        });
      },
    },
  },
  data() {
    return {
      // dateFrom: null,
      // dateTo: null,
      tagsinput: [],
      filteredTags: [],
      // tags: [],
      loadingOperator: false,
      operators: [],
      operator: "",
      searchtext: "",
    };
  },
  //  watch: {
  //   dateFrom(n, o) {
  //     if (!this.isDateAfter(n, this.filters.dateTo)) {
  //       this.filters.dateFrom = new Date(o);
  //       this.notifyWarning('Date is after "Date To". Restored previous date.')
  //     }
  //   },
  //   dateTo(n, o) {
  //     if (!this.isDateAfter(this.filters.dateFrom,n)) {
  //       this.notifyWarning('Date is before "Date From". Restored previous date.')
  //       this.filters.dateTo = new Date(o);
  //     }
  //   },
  // },
  watch: {
    // dateFrom(val) {
    //   // if (
    //   //   this.dateTo != null &&
    //   //   old != null &&
    //   //   val != null &&
    //   //   !this.isDateAfter(val, this.dateTo)
    //   // ) {
    //   //   this.dateFrom = new Date(old);
    //   //   this.notifyWarning('Date is after "Date To". Restored previous date.');
    //   //   return;
    //   // }

    //   var datefromparsed = val !== null ? this.createUTCDatetime(val) : null;
    //   this.$store.commit("chat/setFilter", { dateFrom: datefromparsed });
    //   this.loadChats().catch((e) => {
    //     this.showError(e);
    //   });
    // },
    // dateTo(val) {
    // if (
    //   this.dateFrom != null &&
    //   old != null &&
    //   val != null &&
    //   !this.isDateAfter(this.dateFrom, val)
    // ) {
    //   this.notifyWarning(
    //     'Date is before "Date From". Restored previous date.'
    //   );
    //   this.dateTo = new Date(old);
    //   return;
    // }
    // var datetoparsed =
    //   val !== null ? this.createUTCDateTimeAndAdd(val, 24, "h") : null;
    // this.$store.commit("chat/setFilter", { dateTo: datetoparsed });
    //this.loadChats();
    // },
    operator(val) {
      this.setOperatorFilter(val);
      //this.$store.commit("chat/setFilter", { operator: val });
      this.loadChats().catch((e) => {
        this.showError(e);
      });
    },
  },
};
</script>
