<template>
  <div class="tile">
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
    <div class="tile is-3 is-child">
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
          @input="TagsChanged"
          :open-on-focus="true"
        >
        </b-taginput>
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
    <div class="tile is-2 is-child">
      <b-field label="Date [From]" style="width: 95%">
        <b-datepicker
          v-model="dateFrom"
          rounded
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateFrom = null"
        >
        </b-datepicker>
      </b-field>
    </div>
    <div class="tile is-2 is-child">
      <b-field label="Date [to]" style="width: 95%">
        <b-datepicker
          v-model="dateTo"
          rounded
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateTo = null"
        >
        </b-datepicker>
      </b-field>
    </div>
  </div>
</template>
<style>
.dropdown-content > a {
  text-align: left !important;
}
</style>
<style scoped>
.btable {
  font-size: 13px;
}
article > .panel-heading {
  background: rgb(165, 197, 255);
  background: linear-gradient(
    180deg,
    rgba(165, 197, 255, 1) 0%,
    rgba(40, 127, 207, 1) 100%
  );
}
.tile {
  margin-bottom: 10px;
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";
import { dateMixin } from '../mixins/dateMixin.js';
import notificationsMixin from '../mixins/notificationsMixin.js';
import requestsMixin from "../mixins/requestsMixin.js";
import { tagsMixin } from "../mixins/tagsMixin.js";
export default {
  name: "ChatFilters",
  mixins: [tagsMixin, requestsMixin, dateMixin, notificationsMixin],
  components: {
    //HelloWorld
  },
  methods: {
    doSearch() {
      this.$store.commit("chat/setQuery", this.searchtext);

      this.loadChats();
    },
    TagsChanged() {
      // this.$store.commit("chat/setFilter", { tags: this.tags });
      this.setTagsFilter(this.tags);
      this.loadChats();
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
              resolve()
              this.loadingOperator = false;
            });
        }
        resolve()
      });
    },
    ...mapActions({
      loadChats: "chat/loadChats",
      setTagsFilter: "chat/setTagsFilter",
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
  },
  mounted() {
    if(this.filters.tags)
    {
      this.tags = this.filters.tags
    }
    if(this.filters.dateFrom)
    {
      this.dateFrom = new Date(this.filters.dateFrom)
    }
    if(this.filters.dateTo)
    {
      this.dateTo = new Date(this.filters.dateTo)
    }
    if (this.filters.operator) {
      this.loadOperators().then(() => {
          this.operator = this.filters.operator
      })
    }
  },
  computed: {
    ...mapState("chat", ["filters"]),
  },
  data() {
    return {
      dateFrom: null,
      dateTo: null,
      tagsinput: [],
      filteredTags: [],
      tags: [],
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
    dateFrom(val, old) {
       if (this.dateTo != null && old!= null && val != null && !this.isDateAfter(val, this.dateTo)) {
        this.dateFrom = new Date(old);
        this.notifyWarning('Date is after "Date To". Restored previous date.')
        return
      }

      var datefromparsed = val !== null ? this.createUTCDatetime(val) : null;
      this.$store.commit("chat/setFilter", { dateFrom: datefromparsed });
      this.loadChats();
    },
    dateTo(val, old) {
       if (this.dateFrom != null && old != null && val != null && !this.isDateAfter(this.dateFrom,val)) {
        this.notifyWarning('Date is before "Date From". Restored previous date.')
        this.dateTo = new Date(old)
        return
      }
      var datetoparsed = val !== null ? this.createUTCDateTimeAndAdd(val, 24, 'h') : null;
      this.$store.commit("chat/setFilter", { dateTo: datetoparsed });
      this.loadChats();
    },
    operator(val) {
      this.setOperatorFilter(val);
      //this.$store.commit("chat/setFilter", { operator: val });
      this.loadChats();
    },
  },
};
</script>
