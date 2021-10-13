<template>
  <article style="padding: 2px" id="chatlisttable">
    <span
      class="is-family-sans-serif"
      style="float: left; margin-top: 20px; font-size: 20px"
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
      striped
      narrowed
      detailed
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
      <template #empty v-if="chatsLoading === false"
        >No entries for given criteria.</template
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
        width="160"
        :visible="filters['operator'].display"
      >
        {{ operator(props.row.agent) }}
      </b-table-column>
      <b-table-column
        field="threadid"
        label="Chat ID"
        v-slot="props"
        width="160"
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
        :visible="filters['name'].display"
      >
        <span v-if="props.row.name">{{ props.row.name }}</span
        ><span v-else>{{ props.row.customer.name }}</span>
      </b-table-column>
      <b-table-column
        field="date"
        label="E-mail"
        v-slot="props"
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
        :visible="filters['domain'].display"
      >
        {{ props.row.domain }}
      </b-table-column>
      <b-table-column
        field="customer.geolocation"
        label="Location"
        v-slot="props"
        width="60"
        :visible="filters['location'].display"
      >
        {{
          props.row.customer.geolocation
            ? JSON.parse(props.row.customer.geolocation).country_code
            : ""
        }}
      </b-table-column>
      <b-table-column
        field="customer.ip"
        label="IP"
        v-slot="props"
        :visible="filters['ip'].display"
      >{{ props.row.customer.ip }}</b-table-column>
      <b-table-column
        field=""
        label="Follow up"
        width="100"
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
        label="Order ID"
        width="160"
        v-slot="props"
        :visible="filters['orderid'].display"
      >
        {{ props.row.orderid }}
      </b-table-column>
      <b-table-column
        field="tags"
        label="Extra Points"
        v-slot="props"
        :visible="filters['extrapoints'].display"
      >
        <TablePoints :tags="props.row.tags" />
      </b-table-column>
      <b-table-column
        field="date"
        label="Edit"
        v-slot="props"
        width="80"
        :visible="filters['edit'].display"
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
<style scoped>
</style>
<style >
.extrapointscolumn, .cellcenter
{
  display:block;text-align: center;
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
.is-latefollowup {
  background: rgb(252, 186, 186) !important;
}
.is-cannotoffer {
  background: rgb(254, 255, 190) !important;
}
.is-duplicate {
  background: rgb(219, 219, 219) !important;
}
.is-directsale {
  background: rgb(170, 255, 184) !important;
  color: #000;
}
.btable {
  font-size: 13px;
  padding: -20px;
}
.btable th {
  background: white;

  font-size: 14px;
  color: white !important;
  text-transform: uppercase;
  text-align: center !important;
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
export default {
  name: "ChatListTable",
  components: { TablePoints, TableFollowUp, AddEntryModal },
  mixins: [tablecolorrowsMixin, tableHelper, memberMixin],
  methods: {
    ...mapActions({
      loadChats: "chat/loadChats",
      loadPendingChats: "chat/loadPendingChats",
      getPermissions: "getPermissions",
    }),
    OpenNewChatModal() {
      let that = this;
      this.$buefy.modal.open({
        parent: this,
        component: AddEntryModal,
        hasModalCard: true,
        props: {},
        events: {
          runedition(item) {
            const editmodal = that.$buefy.modal.open({
              parent: that,
              component: ChatItemEditModal,
              hasModalCard: true,
              props: { item },

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
      return row.tags.find((e) => {
        if (e.tag == "duplicate" && e.approved == 1 && this.isAgent()) {
          return true;
        }
      });
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
    showfollowup(row) {
      return row.tags.find((e) => {
        if (e.tag == "wcb" && e.approved == 1 && row.orderid == null) {
          return true;
        }
      });
    },
    calcFollowUp(row) {
      if (row.followup.length > 0) {
        var now = this.moment.utc(
          row.followup[row.followup.length - 1].followupdate
        );
        // var dur = this.moment(now).utc().fromNow()
        // var duration = this.moment.duration(now.diff(end))
        var result = this.moment.utc(now).fromNow();
        return result;
      }
      return "None";
    },
    followup(row) {
      const params = [`module=ChatManager`, `c=FollowUp`, `json=1`].join("&");
      this.$api
        .post(`addonmodules.php?${params}`, {
          threadid: row.id,
        })
        .then((response) => {
          if (response.data.result == "success") {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: "Entry marked",
              type: "is-success",
            });
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data.result,
              type: "is-warning",
            });
          }
        });
    },
    // resetFilters()
    // {
    //   this.$store.commit("chat/setFilter", { dateFrom: null, dateTo: null, operator: null });
    //    this.loadChats();
    // }
  },
  mounted() {
    this.loadChats().catch((e) => {
      this.$buefy.dialog.alert({
        title: "Error",
        message: e,
        type: "is-danger",
        hasIcon: true,
        icon: "close-circle",
        ariaRole: "alertdialog",
        ariaModal: true,
        confirmText: "Reload",
        onConfirm: () => window.location.reload(),
      });
    });
    this.getPermissions();
  },
  computed: {
    ...mapState("chat", ["chats", "chatsPage", "chatsLoading"]),
    ...mapState("chatcolumns", ["filters"]),
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
