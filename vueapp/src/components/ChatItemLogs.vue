<template>
      <article style="" id="chatitemlogs">
      <b-table
        class="modaltable"
        :data="logs.data"
        bordered
        striped
        narrowed
        :total="logs.total"
        paginated
        :per-page="15"
        :loading="this.loading"
        pagination-position="bottom"
      ><template #empty v-if="!this.loading">
        <div class="has-text-centered">No entries</div>
      </template>
        <b-table-column
          field="date"
          label="Date"
          v-slot="props"
          width="100"
          centered 
        >
          {{ parseDateTimeFromUTCtoLocal(props.row.created_at) }}
        </b-table-column>
        <b-table-column
          field="date"
          label="Performed by"
          v-slot="props"
          width="100"
          centered 
        >
          <b-tag type="is-info">{{ props.row.doer.firstname }} {{ props.row.doer.lastname }}</b-tag>
        </b-table-column>
        <!-- <b-table-column
          field="threadid"
          label="Item type"
          v-slot="props"
          width="100"
          centered 
        >
          {{ props.row.itemclass }}
        </b-table-column> -->
       
        <b-table-column
          field="date"
          label="Log"
          v-slot="props"
          width="160"
          
        >
          {{ props.row.desc}}
        </b-table-column>
       
      </b-table></article>
</template>
<style >
#chatitemlogs th span 
{
  margin: 0 auto;
}
.btable {
  font-size: 13px;
}
.btable th
{
  background: rgb(77, 144, 245);
  font-size:14px;
  color:white !important;
  text-transform: uppercase;
  text-align:center !important;
  margin:0 auto;
}
.modaltable {
  border: 1px solid black;
}
.modaltable thead {
  background: rgb(165, 197, 255);
  background: linear-gradient(
    180deg,
    rgba(165, 197, 255, 1) 0%,
    rgba(119, 172, 255, 1) 100%
  );
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";
import { dateMixin } from '../mixins/dateMixin.js'
import "buefy/dist/buefy.css";
export default {
  name: "ChatItemLogs",
  props: ['threadid'],
  mixins: [dateMixin],
  components: {
  },
  methods: {
    ...mapActions('chatlogs', ["loadLogs"]),
  },
  mounted() {
    //this.loadLogs({ itemid: this.threadid})
  },
  computed: {
    ...mapState('chatlogs', ["logs", "loading"]),
  },
  data() {
    return {
      perPage: 25,
      datetimeFromFilter: null,
      datetimeToFilter: null,
    };
  },
};
</script>
