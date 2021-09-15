<template>
  <div class="tile">
    <div class="tile">
      <b-field label="Select datetime range [From]" style="width: 100%">
        <b-datetimepicker
          v-model="dateFrom"
          rounded
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateFrom=null"
          horizontal-time-picker
        >
        </b-datetimepicker>
      </b-field>
    </div>
    <div class="tile">
      <b-field label="Select datetime range [to]" style="width: 100%">
        <b-datetimepicker
          v-model="dateTo"
          rounded
          placeholder="Click to select..."
          icon="calendar-today"
          :icon-right="'close-circle'"
          icon-right-clickable
          @icon-right-click="dateTo=null"
          horizontal-time-picker
        >
        </b-datetimepicker>
      </b-field>
    </div>
  </div>
</template>
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
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions } from "vuex";
export default {
  name: "ChatFilters",
  components: {
    //HelloWorld
  },
  methods: {
  ...mapActions({
      loadChats: "chat/loadChats",
    }),
    clearField(field) {
      this[field] = null
       this.$store.commit("chat/setFilter", {[field]: null});
    },
    constructParams() {
      const params = {
        perPage: this.perPage,
        Datefrom: this.dateFrom,
        Dateto: this.dateTo,
      };
      return params;
    },
    createUTCDatetime(datetime)
    {
      return this.moment(datetime).utc().format('YYYY-MM-DDTHH:mm:SS')+'.000000Z'
    },
    parseDateTime(dateTime) {
      return this.moment(dateTime).format("YYYY-MM-DD HH:DD:SS");
    },
  },
  mounted() {
  },
  computed: {
  //  ...mapState(["chats", "chatsPage", "chatsLoading"]),
  },
  data() {
    return {
      dateFrom: null,
      dateTo: null,
    };
  },
  watch:
  {
    dateFrom(val)
    {
         var datefromparsed = val !== null ? this.createUTCDatetime(val) : null;
         this.$store.commit("chat/setFilter", {dateFrom: datefromparsed});
         this.loadChats()
    },
    dateTo(val)
    {
        var datetoparsed = val !== null ? this.createUTCDatetime(val) : null;
        this.$store.commit("chat/setFilter", {dateTo: datetoparsed});
        this.loadChats()
    }
  }
};
</script>
