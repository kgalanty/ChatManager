<template>
  <article id="orderschatstable">
    <div class="tile">
      <div class="tile is-12 is-child" style="padding: 15px">
        This table shows chats related with completed orders, but still not
        having chats imported into Chat Manager (due to lack of 'sales' tag etc.). If corresponding chat is
        imported, it should disappear from the table below. You can review chats manually in LiveChat and delete inappropriate relations.
        <br />It deletes relation in internal module table only. All other data (order, invoice, service, other stuff in WHMCS) remains untouched.
      </div>
    </div>
    <!-- <img alt="Vue logo" src="../assets/logo.png"> -->
    <b-table
      class="btable"
      :data="orders.data"
      bordered
      narrowed
      :total="orders.total"
      :loading="loading"
      paginated
    >
      <template #empty>
          <b-message type="is-warning" has-icon v-if="!loading">
              No data here yet.
          </b-message>
           <b-message type="is-info" has-icon v-if="loading">
              Loading data...
          </b-message>
        </template>
      <!-- <template #detail="props">
        <article style="text-align: left">
          <StatsDetails :row="props.row" :filters="filters" />
        </article>
      </template> -->
      <b-table-column
        field="lcchatid"
        label="Chat ID"
        v-slot="props"
        width="150"
      >
        <b-button type="is-primary" @click="showAllChats(props.row.lcchatid)">{{
          props.row.lcchatid
        }}</b-button>
      </b-table-column>
      <b-table-column
        field="ordernumber"
        label="Order"
        v-slot="props"
        width="250"
      >
        <b-button
          type="is-link"
          @click="OpenAA(`orders.php?action=view&id=${props.row.ordernumber}`)"
          outlined
        >
          {{ props.row.ordernumber }}
          <b-tag :type="tagClassBasedOnOrderStatus(props.row.order.status)">{{
            props.row.order.status
          }}</b-tag>
        </b-button>
      </b-table-column>
      <b-table-column field="order" label="Service" v-slot="props">
        <span v-if="props.row.order && props.row.order.service.length > 0">
          <b-taglist
            attached
            v-for="service in props.row.order.service"
            :key="service.id"
          >
            <b-tag type="is-info">{{ service.product.name }}</b-tag>
            <b-tag type="is-dark">{{ service.domain }}</b-tag>
            <b-button
              type="is-info"
              @click="OpenAA(`clientsservices.php?id=${service.id}`)"
              icon-right="open-in-new"
              size="is-small"
            />
          </b-taglist>
        </span>
        <span v-if="props.row.order && props.row.order.domain">
          <b-taglist
            attached
            v-for="domain in props.row.order.domain"
            :key="domain.id"
          >
          <b-tag type="is-warning">Domain Registered</b-tag>
            <b-tag type="is-primary">{{ domain.domain }}</b-tag>
            
          </b-taglist>
        </span>
        <span v-if="props.row.order && props.row.order.renewals != ''">
          <b-taglist
            attached
          >
          <b-tag type="is-success">Domain Renewals Count</b-tag>
            <b-tag type="is-primary">{{ props.row.order.renewals.split(",").length }}</b-tag>
            
          </b-taglist>
        </span>
      </b-table-column>
      <b-table-column field="date" label="Date" v-slot="props">
        {{ parseDateTimeFromUTCtoLocal(props.row.date) }}
      </b-table-column>
            <b-table-column field="" label="Actions" v-slot="props">
        <b-button @click="DeleteEntry(props.row.id)" type="is-danger" icon-right="delete">Delete</b-button>
      </b-table-column>
    </b-table>
  </article>
</template>
<style>
.stats-sticky-column {
  background: #ffebb8 !important;
  color: rgb(0, 0, 0) !important;
}
</style>
<style scoped>
.tag {
}
</style>
<style >
.btable {
  font-size: 13px;
}
.btable th {
  font-size: 14px;
  color: rgb(139, 140, 145) !important;
  text-transform: uppercase;
  text-align: center !important;
}
#pendingchatlisttable th span {
  margin: 0 auto;
  text-align: center;
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";
import "buefy/dist/buefy.css";
import { dateMixin } from "@/mixins/dateMixin.js";
//import tableHelper from "../mixins/tableHelper";
import notificationsMixin from "@/mixins/notificationsMixin";
import memberMixin from "@/mixins/memberMixin";
import tableHelper from "../mixins/tableHelper";
import DeleteOrderDataModal from '@/components/DeleteOrderDataModal';
export default {
  name: "OrdersTable",
  mixins: [dateMixin, notificationsMixin, memberMixin, tableHelper],
  components: {},
  methods: {
    ...mapActions("orderschats", ["loadOrders"]),
    DeleteEntry(id)
    {
       this.$buefy.modal.open({
                    parent: this,
                    component: DeleteOrderDataModal,
                    props: { id },
                    width: 'auto',
                    hasModalCard: true,
                    trapFocus: true,
                    customClass: 'lighttheme',
                })
    },
    tagClassBasedOnOrderStatus(status) {
      switch (status) {
        case "Active":
          return "is-success";
        case "Pending":
          return "is-warning";
        case "Cancelled":
          return "is-danger";
        case "Fraud":
          return "is-dark";
      }
      return "is-warning";
    },
  },
  watch: {},
  mounted() {
    // this.loadPendingChats();
    this.loadOrders().catch((e) => {
      this.notifyWarning(e);
    });
  },
  computed: {
    //...mapState("chat", ["pendingchats", "pendingChatsLoading"]),
    ...mapState("orderschats", ["orders", "loading"]),
  },
  data() {
    return {};
  },
};
</script>
