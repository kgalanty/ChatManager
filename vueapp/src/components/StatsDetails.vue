<template>
  <span>
    <strong>Tags Frequency:</strong>
    <ul class="block-list is-outlined is-dark">
      <li
        v-for="(column, index) in row"
        :key="index"
        v-show="index !== 'data' && column > 0"
      >
        <span @click="navigateToFilteredChat(index)"
          ><b-taglist attached 
            ><b-tag type="is-dark">{{ index }}</b-tag
            ><b-tag type="is-info">{{ column }}</b-tag>
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
    }
    // getStats() {
    //   const startDay = this.moment().format("DD") < 16 ? 1 : 16;
    //   this.loading.stats = true
    //   const params = this.generateParamsForRequest('Stats',    [`datefrom=${
    //       this.filters.dateFrom
    //         ? this.createUTCDatetime(this.filters.dateFrom)
    //         : this.createUTCDatetime(
    //             this.moment().format("YYYY-MM-" + startDay)
    //           )
    //     }`,
    //     `dateto=${
    //       this.filters.dateTo
    //         ? this.createUTCDatetime(this.filters.dateTo)
    //         : this.createUTCDatetime(
    //             this.moment().add(1, "months").format("YYYY-MM-01")
    //           )
    //     }`,
    //     `op=${this.agent}`,
    //     `a=StatsDetails`
    //   ])
    //   axios
    //     .get("addonmodules.php?" + params)
    //     .then((response) => {
    //       if (response.data) {
    //         this.data = response.data.data;
    //       }
    //     })
    //     .catch((e) => {
    //       console.log(e);
    //       //window.location = 'login.php'
    //     })
    //     .finally(() => {
    //       this.loading.stats = false;
    //     });
    // },
  },
  mounted() {
   // console.log(`mounted for ${this.agent}`);
    //this.getStats()
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
