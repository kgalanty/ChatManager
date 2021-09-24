<template>
  <article style="background: rgb(152 212 255); padding: 5px">
    <span
      class="is-family-sans-serif"
      style="float: left; margin-top: 20px; font-size: 20px"
      >Chats:</span
    >
    <b-table
      class="btable"
      :data="chats.data"
      bordered
      striped
      narrowed
      :total="chats.total"
      paginated
      backend-pagination
      backend-sorting
      @page-change="onPageChange"
      :per-page="25"
      :loading="this.chatsLoading"
      pagination-position="top"
      :row-class="colorRows"
    >
      <b-table-column field="date" label="Date" v-slot="props" width="160">
        {{ parseDateTime(props.row.date) }}
      </b-table-column>
      <b-table-column field="date" label="Operator" v-slot="props" width="160">
        {{ props.row.agent }}
      </b-table-column>
      <b-table-column
        field="threadid"
        label="Chat ID"
        v-slot="props"
        width="160"
      >
        {{ props.row.threadid }}
      </b-table-column>
      <b-table-column field="tags" label="Tags" v-slot="props" width="160">
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
      <b-table-column field="date" label="All Chats" v-slot="props" width="160">
        <b-button
          type="is-primary"
          outlined
          v-if="props.row.users"
          icon-left="arrow-top-right"
          @click="showAllChats(props.row.users)"
          >Show All Chats</b-button
        >
      </b-table-column>
      <b-table-column field="date" label="Name" v-slot="props" width="160">
        <span v-if="props.row.name">{{ props.row.name }}</span
        ><span v-else>{{ props.row.customer.name }}</span>
      </b-table-column>
      <b-table-column field="date" label="E-mail" v-slot="props" width="160">
        <span v-if="props.row.email">{{ props.row.email }}</span
        ><span v-else> {{ props.row.customer.email }}</span>
      </b-table-column>
      <b-table-column field="date" label="Domain" v-slot="props" width="160">
        {{ props.row.domain }}
      </b-table-column>
      <b-table-column field="date" label="Location" v-slot="props" width="160">
        {{
          props.row.customer.geolocation
            ? JSON.parse(props.row.customer.geolocation).country
            : ""
        }}
      </b-table-column>
      <b-table-column field="date" label="IP" v-slot="props" width="160">
        {{ props.row.customer.ip }}
      </b-table-column>
      <b-table-column field="date" label="Follow up" width="160" v-slot="props" >
        <span v-if="showfollowup(props.row)">
        <b-button type="is-primary" @click="followup(props.row)" v-if="props.row.followup.length < 2"
          >Confirm</b-button
        ><b-field>
              <b-tag type="is-info" style="font-size:14px" v-if="props.row.followup.length < 2" >
                <b-icon icon="alarm" size="is-small"></b-icon>
                &nbsp;&nbsp;{{ calcFollowUp(props.row) }}
                </b-tag>
                </b-field>
                 <b-icon v-if="props.row.followup.length >= 2" icon="check"  type="is-success" ></b-icon>
           </span>
      </b-table-column>
      <b-table-column
        field="orderid"
        label="Order ID"
        width="160"
        v-slot="props"
      >
        {{ props.row.orderid }}
      </b-table-column>
      <b-table-column label="Extra Points" width="160"> points </b-table-column>
      <b-table-column field="date" label="Edit" v-slot="props" width="160">
        <b-button
          type="is-primary"
          icon-left="pencil"
          @click="editModal(props.row)"
          :disabled="isEditActive(props.row)"
          >Edit</b-button
        >
      </b-table-column>
    </b-table>
  </article>
</template>
<style >
.is-latefollowup
{
  background: rgb(255, 129, 129) !important;
}
.is-cannotoffer {
  background: rgb(251, 255, 0) !important;
}
.is-duplicate {
  background: rgb(204, 204, 204) !important;
}
.is-directsale {
  background: rgb(100, 255, 126) !important;
  color: #000;
}
.btable {
  font-size: 13px;
}
.btable th {
  background: rgb(77, 144, 245);
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
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";
import "buefy/dist/buefy.css";
import ChatItemEditModal from "./ChatItemEditModal.vue";
import tablecolorrowsMixin from "../mixins/tablecolorrowsMixin";
export default {
  name: "ChatListTable",
  components: {},
  mixins: [tablecolorrowsMixin],
  methods: {
    ...mapActions({
      loadChats: "chat/loadChats",
      loadPendingChats: "chat/loadPendingChats",
      getPermissions: "getPermissions",
    }),
    showfollowup(row) {
     return row.tags.find((e) => {
        if (e.tag == "wcb" && e.approved == 1 && row.orderid == null) {
          return true;
        }
      })
    },
    calcFollowUp(row)
    {
      if(row.followup.length > 0)
      {
        var now = this.moment.utc(row.followup[row.followup.length-1].followupdate)
       // var dur = this.moment(now).utc().fromNow()
       // var duration = this.moment.duration(now.diff(end))
        var result = this.moment.utc(now).fromNow()
          return result
      }
      return 'None'
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
              message: 'Entry marked',
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
    isEditActive(row) {
      return row.tags.find((e) => {
        if (e.tag == "duplicate" && e.approved == 1 && this.groupMember < 2) {
          return true;
        }
      });
    },
    showAllChats(customerid) {
      window.open("https://my.livechatinc.com/archives/?query=" + customerid);
      //https://my.livechatinc.com/archives/?query=93380b5f-2561-4286-76dd-57a457fe8b5b
    },
    editModal(item) {
      const modal = this.$buefy.modal.open({
        parent: this,
        component: ChatItemEditModal,
        hasModalCard: true,
        props: { item },
        trapFocus: true,
        width: 1200,
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
    this.loadChats().catch((e) => {
      this.$buefy.dialog.alert({
        title: "Error",
        message: e,
        type: "is-danger",
        hasIcon: true,
        icon: "close-circle",
        ariaRole: "alertdialog",
        ariaModal: true,
        confirmText: 'Reload',
         onConfirm: () => window.location.reload()
      });
    });
    this.getPermissions();
  },
  computed: {
    ...mapState("chat", ["chats", "chatsPage", "chatsLoading"], "groupMember"),
    ...mapState(["groupMember"]),
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
