<template>
  <article
    style="
      padding: 2px;
      background: rgb(66 65 118);
      color: #c1bbe3;
      padding: 5px;
      border-radius: 5px;
    "
    id="chatlisttable"
  >
    <span
      class="is-family-sans-serif"
      style="float: left; margin-top: 10px; font-size: 20px"
      >Chats:
      <b-tag rounded type="is-primary" v-if="$store.state.chat.chats.total">{{
        $store.state.chat.chats.total
      }}</b-tag>
      <b-button
        type="is-info"
        size="is-small"
        @click="OpenNewChatModal"
        style="margin-left: 10px; display: inline-block"
        v-if="isAdmin()"
        >Add Chat Manually</b-button
      >
    </span>
    <b-table
      class="btable"
      :data="chats.data"
      bordered
      narrowed
      :detailed="isAdmin()"
      :total="chats.total"
      paginated
      backend-pagination
      backend-sorting
      @page-change="onPageChange"
      :per-page="25"
      :loading="this.chatsLoading"
      pagination-position="both"
      :row-class="colorRows"
      scrollable
    >
      <template #empty v-if="chatsLoading === false" style="text-align:center"
        ><p>No entries for given criteria. Try to loosen the filters.</p>
        <p><b-button type="is-link" @click="clearTagsDates">Clear Tags & Dates</b-button></p>
        </template
      >
      <template #detail="props" v-if="isAdmin()">
        <article style="text-align: left">
          <strong>Notes:</strong> {{ props.row.notes }}
        </article>
      </template>
      <b-table-column
        field="date"
        label="Date"
        v-slot="props"
        width="100"
        :visible="filters['date'].display"
      >
        {{ parseDateTime(props.row.date) }}
      </b-table-column>
      <b-table-column
        field="date"
        label="Operator"
        v-slot="props"
        width="120"
        :visible="filters['operator'].display"
      >
        <b-icon
          style="color: red; margin: 0 auto; display: block"
          icon="close-thick"
          size="is-medium"
          v-if="props.row.agent == 0"
        >
        </b-icon>
        <span v-if="props.row.agentdata"
          >{{ props.row.agentdata.firstname }}
          {{ props.row.agentdata.lastname }}</span
        >
      </b-table-column>
      <b-table-column
        field="threadid"
        label="Chat ID"
        v-slot="props"
        width="50"
        :visible="filters['chatid'].display"
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
        <b-tag v-if="props.row.pending_reviews_count > 0" type="is-info"
          >Pending Reviews: {{ props.row.pending_reviews_count }}</b-tag
        >
      </b-table-column>
      <b-table-column
        field="tags"
        label="Tags"
        v-slot="props"
        width="160"
        :visible="filters['tags'].display"
      >
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
      <b-table-column
        field="date"
        label="All Chats"
        v-slot="props"
        width="100"
        :visible="filters['allchats'].display"
      >
        <b-tooltip label="Click to open LiveChat Archive in new window">
          <b-button
            type="is-primary"
            size="is-small"
            outlined
            v-if="props.row.users"
            icon-left="arrow-top-right"
            @click="showAllChats(props.row.users)"
            >All Chats</b-button
          >
        </b-tooltip>
      </b-table-column>
      <b-table-column
        field="date"
        label="Name"
        v-slot="props"
        width="160"
        :visible="filters['name'].display"
      >
        <span v-if="props.row.name">{{ props.row.name }}</span
        ><span v-else>{{ props.row.customer.name }}</span>
      </b-table-column>
      <b-table-column
        field="email"
        label="E-mail"
        v-slot="props"
        width="250"
        :visible="filters['email'].display"
      >
        <span class="emailTable" v-if="props.row.email">{{
          props.row.email
        }}</span
        ><span v-else class="emailTable">{{ props.row.customer.email }}</span>
      </b-table-column>
      <b-table-column
        field="domain"
        label="Domain"
        v-slot="props"
        width="180"
        :visible="filters['domain'].display"
      >
        {{ props.row.domain }}
      </b-table-column>
      <b-table-column
        field="customer.geolocation"
        label="Location"
        v-slot="props"
        width="50"
        centered
        :visible="filters['location'].display"
      >
        {{
          props.row.customer && props.row.customer[0].geolocation
            ? JSON.parse(props.row.customer[0].geolocation).country_code
            : ""
        }}
      </b-table-column>
      <b-table-column
        field="customer.ip"
        label="IP"
        width="150"
        v-slot="props"
        centered
        :visible="filters['ip'].display"
        >{{ props.row.customer[0].ip }}</b-table-column
      >
      <b-table-column
        field=""
        label="Follow up"
        width="40"
        v-slot="props"
        :visible="filters['followup'].display"
      >
        <TableFollowUp
          class="cellcenter"
          :row="props.row"
          afterClickAction="loadChats"
          style="text-align: center"
        />
      </b-table-column>
      <b-table-column
        field="orderid"
        label="Order/Inv ID"
        width="80"
        v-slot="props"
        cell-class="centernoblock"
        :visible="filters['orderid'].display"
        centered
      >
      <b-taglist attached v-if="props.row.invoiceid" style="display:block;margin-bottom:0 ;">
        <b-tag type="is-danger">I</b-tag>
        <b-tag :type="props.row.invoice && props.row.invoice.status=='Paid' ? 'is-success' : 'is-link'">
          <b-tooltip label="This is invoice ID. Green means Paid.">
            {{props.row.invoiceid}}
            </b-tooltip>
          </b-tag>
      </b-taglist>
        <b-icon
          style="color: red; margin: 0 auto; display: block"
          icon="close-thick"
          size="is-medium"
          v-if="colorDirectConvertedSaleLackOrder(props.row)"
        >
        </b-icon>
       <b-tag type="is-info" v-if="props.row.orderid">{{ props.row.orderid }}</b-tag>
      </b-table-column>
      <b-table-column
        field="tags"
        label="Extra Points"
        v-slot="props"
        :visible="filters['extrapoints'].display"
        width="80"
      >
        <TablePoints
          v-if="props.row.agent != 0"
          :tags="props.row.tags"
          :invoice="props.row.invoice"
          :invoiceStatus="props.row.order ? props.row.order.invoice.status : (props.row.invoice ? props.row.invoice.status : '')"
        />
      </b-table-column>
      <b-table-column
        field="date"
        label="Edit"
        v-slot="props"
        width="40"
        :visible="filters['edit'].display"
        centered
      >
        <b-button
          type="is-primary"
          icon-left="pencil"
          @click="editModal(props.row)"
          :disabled="isEditActive(props.row)"
        ></b-button>
      </b-table-column>
    </b-table>
  </article>
</template>
<style>
.is-empty
{
  text-align:center;
}
.extrapointscolumn,
.cellcenter {
  display: block;
  text-align: center;
}
.centernoblock {
  text-align: center;
}
.emailTable {
  overflow-wrap: anywhere !important;
  word-wrap: anywhere !important;
  hyphens: auto !important;
}

#chatlisttable .table {
  border-collapse: collapse !important;
}
#chatlisttable .table td {
  border: 1px solid rgb(255, 255, 255);
  margin: 3px;
}
#chatlisttable th span {
  margin: 0 auto;
  text-align: center;
}
#chatlisttable th {
  border: 0;
}
.table{
  overflow:hidden;
}
.btable {
  font-size: 0.9rem;
  padding: -20px;
  
}
.btable th {
  background: white;
  font-size: 0.6rem;
  color: white !important;
  text-transform: uppercase;
  text-align: center !important;
}

</style>

<script>
/* eslint-disable vue/no-unused-components */
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";
import "buefy/dist/buefy.css";
import ChatItemEditModal from "./ChatItemEditModal.vue";
import TableFollowUp from "./TableFollowUp.vue";
import TablePoints from "./TablePoints.vue";
import tablecolorrowsMixin from "../mixins/tablecolorrowsMixin";
import memberMixin from "../mixins/memberMixin";
import tableHelper from "../mixins/tableHelper";
import AddEntryModal from "./AddEntryModal.vue";
import requestMixin from "../mixins/requestsMixin";
export default {
  name: "ChatListTable",
  components: { TablePoints, TableFollowUp, AddEntryModal },
  mixins: [tablecolorrowsMixin, tableHelper, memberMixin, requestMixin],
  methods: {
    ...mapActions({
      loadChats: "chat/loadChats",
      loadPendingChats: "chat/loadPendingChats",
      getPermissions: "getPermissions",
      setTagsFilter: "chat/setTagsFilter",
    }),
    clearTagsDates()
    {
      this.$store.commit("chat/setFilter", { dateFrom: null, dateTo: null,  });
      this.setTagsFilter([])
      this.loadChats().catch((e) => {
        this.showError(e);
      });
    },
    OpenNewChatModal() {
      let that = this;
      this.$buefy.modal.open({
        parent: this,
        component: AddEntryModal,
        hasModalCard: true,
        props: {},
        customClass: this.darkstyle ? 'darktheme' : 'lighttheme',
        events: {
          runedition(item) {
            const editmodal = that.$buefy.modal.open({
              parent: that,
              component: ChatItemEditModal,
              hasModalCard: true,
              props: { item },
              customClass: that.darkstyle ? 'darktheme' : 'lighttheme',
              trapFocus: true,
              width: "auto",
            });
            editmodal.$on("close", () => {
              that.loadChats();
              that.loadPendingChats();
            });
          },
        },
        trapFocus: true,
        width: "auto",
      });
    },

    isEditActive(row) {
      return (
        row.tags.find((e) => {
          if (e.tag == "duplicate" && e.approved == 1 && this.isAgent()) {
            return true;
          }
        }) ||
        (row.agent != this.aid && this.isAgent())
      );
    },

    editModal(item) {
      const modal = this.$buefy.modal.open({
        parent: this,
        component: ChatItemEditModal,
        hasModalCard: true,
        props: { item },
        trapFocus: true,
        width: "auto",
        customClass: this.darkstyle ? 'darktheme' : 'lighttheme'
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

    // resetFilters()
    // {
    //   this.$store.commit("chat/setFilter", { dateFrom: null, dateTo: null, operator: null });
    //    this.loadChats();
    // }
  },
  mounted() {
    this.getPermissions();
    // this.loadChats().catch((e) => {
    //   this.$buefy.dialog.alert({
    //     title: "Error",
    //     message: e,
    //     type: "is-danger",
    //     hasIcon: true,
    //     icon: "close-circle",
    //     ariaRole: "alertdialog",
    //     ariaModal: true,
    //     confirmText: "Reload",
    //     onConfirm: () => window.location.reload(),
    //   });
    // });
  },
  computed: {
    ...mapState("chat", ["chats", "chatsPage", "chatsLoading"]),
    ...mapState("chatcolumns", ["filters"]),
    ...mapState(["aid", "darkstyle"]),
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
