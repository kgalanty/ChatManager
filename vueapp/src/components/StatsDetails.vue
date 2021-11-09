<template>
  <span>
    <strong>Tags Frequency:</strong>
    <ul v-show="data && data.length==0">
      <li><b-skeleton width="30%" animated></b-skeleton></li>
      <li><b-skeleton width="30%" animated></b-skeleton></li>
      <li><b-skeleton width="30%" animated></b-skeleton></li>
      </ul>
    <ul class="block-list is-outlined is-dark">
      <li
        v-for="(column, index) in data"
        :key="index"
      >
        <span @click="navigateToFilteredChat(column.tag)"
          ><b-taglist attached 
            ><b-tag type="is-dark">{{ column.tag }}</b-tag
            ><b-tag type="is-info">{{ column.count }}</b-tag>
          </b-taglist></span
        >
      </li>
      <li v-if="row.data.cm_points > 0"><b-taglist attached 
            ><b-tag type="is-success">Stayed Cancellation Requests</b-tag
            ><b-tag type="is-info">{{ row.data.cm_points }}</b-tag>
          </b-taglist></li>
    </ul>
  </span>
</template>
<style>
.dropdown-content > a {
  text-align: left !important;
}
</style>
<style scoped>
.block-list li {
  padding: 1px;
  background: #f5f5f5;
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
//import axios from "axios";
import { dateMixin } from "@/mixins/dateMixin";
import requestsMixin from "@/mixins/requestsMixin";
import router from '@/router/'
import axios from "axios"
import { mapActions } from 'vuex';
export default {
  name: "StatsDetails",
  props: ["row", "filters"],
  mixins: [dateMixin, requestsMixin],
  components: {
    //HelloWorld
  },
  methods: {
    ...mapActions({
      setTagsFilter: "chat/setTagsFilter",
      setOperatorFilter: "chat/setOperatorFilter",
      setDateFromFilter: "chat/setDateFromFilter",
      setDateToFilter: "chat/setDateToFilter",
    }),
    navigateToFilteredChat(tag)
    {
      this.setTagsFilter([tag])
      this.setOperatorFilter(this.row.data.agent)
      this.setDateFromFilter(this.createUTCDatetime(this.filters.dateFrom))
      this.setDateToFilter(this.createUTCDatetime(this.filters.dateTo))
      router.push({ path: '/' })
    },
    getStatsDetails( agentid) {
      
      const params = this.generateParamsForRequest('Stats',    [`datefrom=${
           this.createUTCDatetime(this.filters.dateFrom)
        }`,
        `dateto=${
         this.createUTCDateTimeAndAdd(this.filters.dateTo, 24, 'h')
        }`,
        `op=${agentid}`,
        `a=StatsDetails`
      ])
      axios
        .get("addonmodules.php?" + params)
        .then((response) => {
          if (response.data) {
            this.data = response.data.data;
          }
        })
        .catch((e) => {
          console.log(e);
          //window.location = 'login.php'
        })
        .finally(() => {
        });
    },
  },
  mounted() {
    this.getStatsDetails(this.row.data.agent)
  },
  computed: {
  },
  data() {
    return {
      loading: {
        stats: false,
      },
      data: [],
    };
  },
};
</script>
