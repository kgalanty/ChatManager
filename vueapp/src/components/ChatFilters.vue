<template>
  <div class="tile">
    <div class="tile is-3 is-child">
      <b-field label="Tags" style="width: 95%">
        <b-taginput
          v-model="tags"
          :data="filteredTags"
          autocomplete
          field="tag.tag"
          icon="label"
          type="is-info"
          placeholder="Add a tag"
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
        <option value="">-</option>
          <option :value="op.email" :key="i" v-for="(op, i) in operators">
            {{ op.firstname }} {{ op.lastname }}
          </option>
        </b-select>
      </b-field>
    </div>
    <div class="tile is-3 is-child">
      <b-field label="Select datetime range [From]" style="width: 95%">
        <b-datetimepicker
          v-model="dateFrom"
          rounded
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateFrom = null"
          horizontal-time-picker
        >
        </b-datetimepicker>
      </b-field>
    </div>
    <div class="tile is-3 is-child">
      <b-field label="Select datetime range [to]" style="width: 95%">
        <b-datetimepicker
          v-model="dateTo"
          rounded
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateTo = null"
          horizontal-time-picker
        >
        </b-datetimepicker>
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
import { mapActions } from "vuex";
import { tagsMixin } from "../mixins/tagsMixin.js";
export default {
  name: "ChatFilters",
  mixins: [tagsMixin],
  components: {
    //HelloWorld
  },
  methods: {
    TagsChanged() {
      this.$store.commit("chat/setFilter", { tags: this.tags });
      this.loadChats();
    },
    getFilteredTags(text) {
      this.filteredTags = this.tagsList.filter((option) => {
        return option.toString().toLowerCase().indexOf(text.toLowerCase()) >= 0;
      });
    },
    loadOperators() {
      if (this.operators.length == 0) {
        this.loadingOperator = true;
        this.$api
          .get(
            `addonmodules.php?module=ChatManager&c=Agents&json=1&a=GetAgentsList&q=`
          )
          .then(({ data }) => {
            this.operators = data.data;
          })
          .catch((error) => {
            this.operators = [];
            throw error;
          })
          .finally(() => {
            this.loadingOperator = false;
          });
      }
    },
    ...mapActions({
      loadChats: "chat/loadChats",
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
  mounted() {},
  computed: {
    //  ...mapState(["chats", "chatsPage", "chatsLoading"]),
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
    };
  },
  watch: {
    dateFrom(val) {
      var datefromparsed = val !== null ? this.createUTCDatetime(val) : null;
      this.$store.commit("chat/setFilter", { dateFrom: datefromparsed });
      this.loadChats();
    },
    dateTo(val) {
      var datetoparsed = val !== null ? this.createUTCDatetime(val) : null;
      this.$store.commit("chat/setFilter", { dateTo: datetoparsed });
      this.loadChats();
    },
    operator(val) {
      this.$store.commit("chat/setFilter", { operator: val });
      this.loadChats();
    },
  },
};
</script>
