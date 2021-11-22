<template>
  <div class="panel-block loading-container">
    <article
      class="panel column is-full"
      style="width: 100% !important"
    ><p
        class="panel-heading"
      >
        Orders-Chats relations
      </p>
      <OrdersTable></OrdersTable>
    </article>
  </div>
</template>
<style scoped>

</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
//import { mapActions, mapState } from "vuex";
// import ChatListTable from '../components/ChatListTable.vue'
// import PendingChatListTable from '../components/PendingChatListTable.vue'
// import ChatFilters from '../components/ChatFilters.vue'
 import OrdersTable from '@/components/OrdersTable.vue'
 import { mapActions } from "vuex";
import "buefy/dist/buefy.css";
export default {
  
  name: "Orders",
  components: {
    OrdersTable
  },
  methods: {
   ...mapActions({
      getPermissions: "getPermissions",
    }),
  },
  mounted() {
    this.getPermissions()
  },
  computed: {
  },
  data() {
    return {
     
     
    };
  },
};
</script>
