<template>
  <div class="panel-block loading-container">
    <article
      class="panel column is-full"
      style="width: 100% !important"
    ><p
        class="panel-heading"
      >
        Stats
      </p>
      <StatsTable></StatsTable>
    </article>
  </div>
</template>
<style scoped>
.panel-heading
{
  padding-left: 0px !important; margin-bottom: 5px;
}

article > .panel-heading {
  background: rgb(226 226 226);
  
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
//import { mapActions, mapState } from "vuex";
// import ChatListTable from '../components/ChatListTable.vue'
// import PendingChatListTable from '../components/PendingChatListTable.vue'
// import ChatFilters from '../components/ChatFilters.vue'
 import StatsTable from '@/components/StatsTable.vue'
import "buefy/dist/buefy.css";
export default {
  name: "Stats",
  components: {
    StatsTable
  },
  methods: {
  
  },
  mounted() {
  },
  computed: {
  },
  data() {
    return {
     
     
    };
  },
};
</script>
