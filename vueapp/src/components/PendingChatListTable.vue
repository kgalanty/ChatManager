<template>
  <article
    id="pendingchatlisttable"
    style="background: #f14668; color: white; padding: 5px; border-radius: 5px"
    v-if="pendingchats && pendingchats.data && pendingchats.data.length > 0"
  >
    <span
      class="is-family-sans-serif"
      style="float: left; margin-top: 20px; font-size: 20px"
      >Pending for Review:</span
    >
    <!-- <img alt="Vue logo" src="../assets/logo.png"> -->
    <b-table
      class="btable"
      :data="pendingchats.data"
      bordered
      striped
      narrowed
      :total="pendingchats.total"
      paginated
      :per-page="25"
      :loading="this.pendingChatsLoading"
      pagination-position="top"
      :row-class="colorRows"
    >
      <b-table-column field="date" label="Date" v-slot="props" width="100">
        {{ parseDateTime(props.row.date) }}
      </b-table-column>
      <b-table-column field="date" label="Operator" v-slot="props">
        {{ operator(props.row.agent) }}
      </b-table-column>
      <b-table-column
        field="threadid"
        label="Chat ID"
        v-slot="props"
        width="160"
      >
        <b-tooltip label="Click to open chat in LiveChat (new window)">
          <b-button
            type="is-primary"
            size="is-small"
            outlined
            v-if="props.row.users"
            icon-left="arrow-top-right"
            @click="showAllChats(props.row.threadid)"
            >{{ props.row.threadid }}</b-button
          >
        </b-tooltip>
      </b-table-column>
      <b-table-column field="tags" label="Tags" v-slot="props">
        <b-taglist>
          <span
            :key="index"
            style="display: inline-block; margin: 2px"
            v-for="(tagObject, index) in props.row.tags"
          >
            <b-tag
              :type="tagObject.approved == 1 ? 'is-warning' : 'is-light'"
              >{{ tagObject.tag }}</b-tag
            ></span
          >
        </b-taglist>
      </b-table-column>
      <b-table-column field="date" label="All Chats" v-slot="props">
        <b-button
          type="is-primary"
          size="is-small"
          outlined
          v-if="props.row.users"
          icon-left="arrow-top-right"
          @click="showAllChats(props.row.users)"
          >All Chats</b-button
        >
      </b-table-column>
      <b-table-column field="date" label="Name" v-slot="props" width="160">
        <span v-if="props.row.name">{{ props.row.name }}</span
        ><span v-else>{{ props.row.customer.name }}</span>
      </b-table-column>
      <b-table-column field="date" label="E-mail" v-slot="props">
        <span v-if="props.row.email" class="emailTable">{{ props.row.email }}</span
        ><span v-else class="emailTable"> {{ props.row.customer.email }}</span>
      </b-table-column>
      <b-table-column field="date" label="Domain" v-slot="props" width="160">
        {{ props.row.domain }}
      </b-table-column>
      <b-table-column field="date" label="Location" v-slot="props" width="30">
        {{
          props.row.customer.geolocation
            ? JSON.parse(props.row.customer.geolocation).country_code
            : ""
        }}
      </b-table-column>
      <b-table-column field="date" label="IP" v-slot="props" width="160">
        {{ props.row.customer.ip }}
      </b-table-column>
      <b-table-column label="Follow up" width="160" v-slot="props">
        <TableFollowUp
          :row="props.row"
          afterClickAction="loadPendingChats"
          style="text-align: center"
        />
      </b-table-column>
      <b-table-column
        field="orderid"
        label="Order ID"
        width="160"
        v-slot="props"
      >
        {{ props.row.orderid }}
      </b-table-column>
      <b-table-column label="Extra Points" width="160" v-slot="props" ><TablePoints :tags="props.row.tags" /></b-table-column>
      <b-table-column field="date" label="Edit" v-slot="props" width="60">
        <b-button
          type="is-primary"
          icon-left="pencil"
          @click="editModal(props.row)"
          ></b-button
        >
      </b-table-column>
    </b-table>
  </article>
</template>
<style scoped>
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
import ChatItemEditModal from "./ChatItemEditModal.vue";
import TableFollowUp from "./TableFollowUp.vue";
import tablecolorrowsMixin from "../mixins/tablecolorrowsMixin";
import TablePoints from "./TablePoints.vue";
import tableHelper from "../mixins/tableHelper";
export default {
  name: "PendingChatListTable",
  mixins: [tablecolorrowsMixin, tableHelper],
  components: { TableFollowUp, TablePoints },
  methods: {
    ...mapActions("chat", ["loadPendingChats", "loadChats"]),
    isEditActive(row) {
      row.tags.find((e) => {
        if (e.tag == "duplicate" && e.approved == 1) {
          return false;
        }
      });
      return true;
    },

    editModal(item) {
      const modal = this.$buefy.modal.open({
        parent: this,
        component: ChatItemEditModal,
        hasModalCard: true,
        props: { item },
        trapFocus: true,
        width: "auto",
      });
      modal.$on("close", () => {
        this.loadChats();
        this.loadPendingChats();
      });
    },
    onPageChange(page) {
      this.$store.commit("chat/setChatsPage", page);
      this.loadChats();
    },
    parseDateTime(dateTime) {
      return this.moment(dateTime).format("YYYY-MM-DD HH:mm:SS");
    },
  },
  mounted() {
    this.loadPendingChats();
  },
  computed: {
    ...mapState("chat", ["pendingchats", "pendingChatsLoading"]),
    //...mapState('chatcolumns', ['filters']),
  },
  data() {
    return {
      perPage: 25,
      datetimeFromFilter: null,
      datetimeToFilter: null,
      total: 5,
    };
  },
};
</script>
