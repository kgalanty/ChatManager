<template>
<article style="background:#ff7373;padding: 5px;
" v-if="pendingchats.data != []">
  <span class="is-family-sans-serif" style="float:left; margin-top:20px;font-size:20px">Pending for Review:</span>
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
      >
        <b-table-column
          field="date"
          label="Date"
          v-slot="props"
          width="160"
          
        >
          {{ parseDateTime(props.row.date) }}
        </b-table-column>
        <b-table-column
          field="date"
          label="Operator"
          v-slot="props"
          width="160"
          
        >
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
        <b-table-column
          field="tags"
          label="Tags"
          v-slot="props"
          width="160"
          
        >
          <b-taglist>
              <span :key="index" style="display:inline-block;margin:2px;"
              v-for="(tagObject, index) in props.row.tags">
            <b-tag
             :type="tagObject.approved==1? 'is-warning' : 'is-light'"
              >{{ tagObject.tag }}</b-tag
            ></span>
            
            </b-taglist>
        </b-table-column>
        <b-table-column
          field="date"
          label="All Chats"
          v-slot="props"
          width="160"
          
        >
          <b-button
            type="is-primary"
            outlined
            v-if="props.row.users"
            icon-left="arrow-top-right"
            @click="showAllChats(props.row.users)"
            >Show All Chats</b-button
          >
        </b-table-column>
        <b-table-column
          field="date"
          label="Name"
          v-slot="props"
          width="160"
          
        >
         <span v-if="props.row.name">{{ props.row.name }}</span><span v-else>{{ props.row.customer.name }}</span>
        </b-table-column>
        <b-table-column
          field="date"
          label="E-mail"
          v-slot="props"
          width="160"
          
        >
        <span v-if="props.row.email">{{props.row.email}}</span><span v-else>
          {{ props.row.customer.email }}</span>
        </b-table-column>
        <b-table-column
          field="date"
          label="Domain"
          v-slot="props"
          width="160"
          
        >
          {{ props.row.domain }}
        </b-table-column>
        <b-table-column
          field="date"
          label="Location"
          v-slot="props"
          width="160"
          
        >
          {{
            props.row.customer.geolocation
              ? JSON.parse(props.row.customer.geolocation).country
              : ""
          }}
        </b-table-column>
        <b-table-column
          field="date"
          label="IP"
          v-slot="props"
          width="160"
          
        >
          {{ props.row.customer.ip }}
        </b-table-column>
        <b-table-column field="date" label="Follow up" width="160" >
          <b-button type="is-primary">Action</b-button>
        </b-table-column>
        <b-table-column field="orderid" label="Order ID" width="160"  v-slot="props">
          {{ props.row.orderid }}
        </b-table-column>
        <b-table-column label="Extra Points" width="160" >
          points
        </b-table-column>
        <b-table-column
          field="date"
          label="Edit"
          v-slot="props"
          width="160"
          
        >
          <b-button
            type="is-primary"
            icon-left="pencil"
            @click="editModal(props.row)"
            >Edit</b-button
          >
        </b-table-column>
      </b-table>
</article>
</template>
<style >
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
import ChatItemEditModal from './ChatItemEditModal.vue'
export default {
  name: "PendingChatListTable",
  components: {
  },
  methods: {
    ...mapActions('chat', ["loadPendingChats", 'loadChats']),
    showAllChats(customerid) {
      window.open("https://my.livechatinc.com/archives/?query=" + customerid);
      //https://my.livechatinc.com/archives/?query=93380b5f-2561-4286-76dd-57a457fe8b5b
    },
    editModal(item)
    { 
      
       const modal = this.$buefy.modal.open({
        parent: this,
        component: ChatItemEditModal,
        hasModalCard: true,
        props: {item},
        trapFocus: true,
        width:1200
      });
      modal.$on('close', () => 
      {
        this.loadChats()
        this.loadPendingChats()
      }
      )
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
    this.loadPendingChats()
  },
  computed: {
    ...mapState('chat', ["pendingchats", "pendingChatsLoading"]),
  },
  data() {
    return {
      perPage: 25,
      datetimeFromFilter: null,
      datetimeToFilter: null,
      total: 5,
      // data: [
      //   {
      //     id: 1,
      //     first_name: "Jesse",
      //     last_name: "Simmons",
      //     date: "2016-10-15 13:43:27",
      //     gender: "Male",
      //   },
      //   {
      //     id: 2,
      //     first_name: "John",
      //     last_name: "Jacobs",
      //     date: "2016-12-15 06:00:53",
      //     gender: "Male",
      //   },
      //   {
      //     id: 3,
      //     first_name: "Tina",
      //     last_name: "Gilbert",
      //     date: "2016-04-26 06:26:28",
      //     gender: "Female",
      //   },
      //   {
      //     id: 4,
      //     first_name: "Clarence",
      //     last_name: "Flores",
      //     date: "2016-04-10 10:28:46",
      //     gender: "Male",
      //   },
      //   {
      //     id: 5,
      //     first_name: "Anne",
      //     last_name: "Lee",
      //     date: "2016-12-06 14:38:38",
      //     gender: "Female",
      //   },
      // ],
    };
  },
};
</script>
