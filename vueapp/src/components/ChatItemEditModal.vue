<template>
  <form action="">
    <div class="modal-card">
      <header class="modal-card-head">
        <p class="modal-card-title">Edit chat {{ item.threadid }}</p>
        <button type="button" class="delete" @click="$emit('close')" />
      </header>
      <section class="modal-card-body">
        <!-- <div
        class="current"
        >
          Currently selected client:
          {{ currentClient ? currentClient : "none" }}
          <br />
          Currently selected service: {{ currentService ? currentService : 'none'}}
          <br />
          Currently set order: {{ currentOrder ? currentOrder : 'none'}}
        </div> -->

        <b-tabs type="is-toggle" expanded>
          <b-tab-item label="Customer" icon="google-photos">
            <b-field label="Client Name">
              <b-input v-model="name" placeholder="Fill client name"></b-input>
            </b-field>
            <b-field label="Client E-mail">
              <b-input
                v-model="email"
                placeholder="Fill e-mail address"
              ></b-input>
            </b-field>
            <b-field label="Domain">
              <b-input v-model="domain" placeholder="Fill domain"></b-input>
            </b-field>

            <!-- <b-autocomplete
            :data="clients"
            placeholder="Search for a client"
            field="email"
            :loading="isFetchingClients"
            @typing="getAsyncData"
            @select="(option) => (selectedClient = option)"
          >
            <template slot-scope="props">
              <div class="media">
                <div class="media-content">
                  #{{ props.option.id }}
                  <strong>{{
                    props.option.firstname + " " + props.option.lastname
                  }}</strong>
                  <br />{{ props.option.email }} <br /><i>{{
                    props.option.companyname
                  }}</i>
                </div>
              </div>
            </template>
          </b-autocomplete>
        
        
        <b-field label="Service">
          <b-select
            :placeholder="
              services.length > 0 ? 'Select a service' : 'Select a client first'
            "
            expanded
            v-model="selectedService"
            @change="selectedOrder = option.orderid"
          >
            <option value="" v-if="services.length == 0"><i>Empty</i></option> 
            <option
              :value="option.id"
              :key="option.id"
              v-for="option in services"
            >
              #{{ option.id }} {{ option.product.name }}
              <span v-if="option.domain">({{ option.domain }})</span>
            </option>
          </b-select>
        </b-field>
         -->
            <b-field label="Order" :type="OrderStatusField">
              <b-input
                v-model="selectedOrder"
                placeholder="Fill order number"
                expanded
              ></b-input>
              <b-button
                style="float: left"
                icon-right="magnify"
                type="is-info"
                :loading="loadingCheckBtn"
                @click="checkOrder"
              />
            </b-field>
            <b-field label="Cannot offer reason" v-if="HasCustomOffer">
              <b-select
                @change="cannotofferCustom = ''"
                placeholder="Choose reason"
                v-model="cannotoffer"
                expanded
              >
                <option
                  :value="tag"
                  :key="i"
                  v-for="(tag, i) in cannotofferReasons"
                  :selected="cannotoffer == tag"
                >
                  {{ tag }}
                </option>
              </b-select>
              <b-input
                v-if="cannotoffer == 'Other'"
                v-model="cannotofferCustom"
                placeholder="Fill custom offer/reason"
                expanded
              ></b-input>
            </b-field>
             <b-field label="Agent">
            <b-autocomplete
                v-model="agent"
                ref="autocomplete"
                :data="filteredAgentArray"
                @select="option => selectedAgent = option"
                field="email"
                @typing="getAgents"
                :loading="isFetchingAgents"
                >
                <template slot-scope="props">
                    <div class="media">
                       {{ props.option.firstname }} {{ props.option.lastname }} ({{ props.option.email }})
                    </div>
                </template>
                <template #empty>No results for {{agent}}</template>
            </b-autocomplete>
        </b-field>
            <b-field label="Notes">
              <b-input type="textarea" v-model="notes"></b-input>
            </b-field>
          </b-tab-item>
          <b-tab-item label="Tags" icon="google-photos">
            <b-field label="Tags">
              <b-table
                class="modaltable"
                :data="tags"
                bordered
                striped
                narrowed
              >
                <b-table-column
                  field="date"
                  label="Tag"
                  v-slot="props"
                  sortable
                >
                  <b-tag
                    :type="props.row.approved == 1 ? 'is-warning' : 'is-light'"
                    >{{ props.row.tag }}</b-tag
                  >
                  <b-tooltip
                    label="This tag is proposed to delete"
                    position="is-right"
                    v-if="props.row.proposed_deletion == 1"
                  >
                    <b-icon icon="delete" size="is-small"> </b-icon>
                  </b-tooltip>
                  <span style="float: right">
                    <b-button
                      icon-right="check"
                      type="is-success"
                      style="font-size: 0.8rem !important"
                      @click="approveTag(props.row)"
                      v-if="groupMember === 2 && props.row.approved == 0" />
                    <b-button
                      icon-right="delete"
                      type="is-danger"
                      style="font-size: 0.8rem !important"
                      @click="deleteTag(props.row)"
                  /></span>
                </b-table-column>
              </b-table>
            </b-field>
            <b-field label="Add new Tag">
              <b-select
                placeholder="Add new Tag"
                style="float: left"
                v-model="newtag"
                expanded
              >
                <option :value="tag" :key="i" v-for="(tag, i) in tagsList">
                  {{ tag }}
                </option> </b-select
              ><b-button
                style="float: left"
                icon-right="plus"
                type="is-info"
                @click="addtag"
              />
            </b-field>

            <b-collapse :open="false" position="is-top" animation="slide">
              <template #trigger="props">
                <a aria-controls="contentIdForA11y1">
                  <h2 class="label">
                    <b-icon
                      :icon="!props.open ? 'menu-down' : 'menu-up'"
                    ></b-icon>
                    Tag Log
                  </h2>
                </a>
              </template>

              <b-field label="" v-if="groupMember == 2">
                <b-table
                  class="modaltable"
                  :data="tagslog"
                  narrowed
                  :per-page="5"
                  :loading="tagsLogLoading"
                >
                  <template #empty>
                    <div class="has-text-centered">No entries</div>
                  </template>
                  <b-table-column field="date" label="Tag" v-slot="props">
                    <b-tag type="is-warning">{{ props.row.tag }}</b-tag>
                  </b-table-column>
                  <b-table-column
                    field="date"
                    label="Performed by"
                    v-slot="props"
                  >
                    <b-tag type="is-primary">
                      {{ props.row.doer.firstname }}
                      {{ props.row.doer.lastname }}</b-tag
                    >
                  </b-table-column>
                  <b-table-column field="action" label="Action" v-slot="props">
                    <b-tag type="is-info">{{ props.row.action }}</b-tag>
                  </b-table-column>
                  <b-table-column field="date" label="Date" v-slot="props">
                    {{
                      moment
                        .utc(props.row.created_at, "YYYY-MM-DD HH:mm:ss")
                        .local()
                        .format("DD.MM.YYYY HH:mm:ss")
                    }}
                  </b-table-column>
                </b-table>
              </b-field>
            </b-collapse>
          </b-tab-item>
          <b-tab-item :headerClass="reviewTabHeader">
            <template #header >
                <b-icon icon="source-pull"></b-icon>
                <span> Review 
                  <b-tag rounded type="is-primary"> {{ reviewRequests.length }} </b-tag>
                
                 </span>
            </template>
         <b-notification
            type="is-warning"
            has-icon
            v-if="reviewStatus==1"
            :closable="false"
            role="alert">
            This thread has pending reviews.
        </b-notification>
            <div class="notification is-primary">
              <div class="buttons">
                Here you can send this chat to review by supervisors. Enter
                comment and use the button to proceed.
              </div>
              <b-field label="Comment">
                <b-input
                  v-model="commmentReview"
                  type="textarea"
                  placeholder="Comment (required)"
                ></b-input>
              </b-field>
              <div class="buttons">
                <b-button
                  type="is-primary"
                  inverted
                  outlined
                  @click="sendToReview"
                  :loading="sendReviewLoadingBtn"
                  >Send to review</b-button
                >
              </div>
            </div>

                <b-table
                  class="modaltable"
                  :data="reviewRequests"
                  narrowed
                  :per-page="5"
                  v-if="groupMember == 2"
                >
                  <template #empty>
                    <div class="has-text-centered">No entries</div>
                  </template>
                 <b-table-column
                    field="pending"
                    label="Seen"
                    v-slot="props"
                  >
                    <b-icon icon="close" v-if="props.row.pending==1" size="is-small" type="is-danger"></b-icon>
                    <b-icon icon="check" v-if="props.row.pending==0" size="is-small" type="is-success"></b-icon>
                  </b-table-column>
                  <b-table-column
                    field="date"
                    label="Performed by"
                    v-slot="props"
                  >
                    <b-tag type="is-primary">
                      {{ props.row.doer.firstname }}
                      {{ props.row.doer.lastname }}</b-tag
                    >
                  </b-table-column>
                  <b-table-column field="comment" label="Comment" v-slot="props">
                    {{ props.row.comment }}
                  </b-table-column>
                  <b-table-column field="date" label="Date" v-slot="props">
                    {{
                      parseDateTimeFromUTCtoLocal(props.row.created_at)
                    }}
                  </b-table-column>
                   <b-table-column field="date" label="Actions" v-slot="props">
                     <b-button type="is-success" size="is-small" icon-right="check" v-if="props.row.pending==1" @click="MarkReviewComment(props.row.id)" ></b-button>
                  </b-table-column>
                </b-table>

          </b-tab-item>
        </b-tabs>
      </section>
      <footer class="modal-card-foot">
        <b-button label="Close" @click="$emit('close')" />
        <b-button
          label="Save changes"
          type="is-primary"
          @click="save"
          :loading="saveLoadingBtn"
        />
      </footer>
    </div>
  </form>
</template>
<script>
import debounce from "lodash/debounce";
import { mapActions, mapState } from "vuex";
import { dateMixin } from '../mixins/dateMixin.js'
export default {
  name: "ChatItemEditModal",
  props: ["item"],
  mixins: [dateMixin],
  components: {},
  computed: {
    ...mapState(["groupMember"]),
    reviewTabHeader()
    {
      if(this.reviewStatus == 1)
      {
        return 'reviewTabHeader';
      }
      return ''
    }
  },
  methods: {
    ...mapActions({
      loadChats: "chat/loadChats",
      getPermissions: "getPermissions",
    }),
    getAgents: debounce(function (name) {
      this.isFetchingAgents = true
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=Agents&json=1&a=GetAgentsList&q=${name}`
        )
        .then(({ data }) => {
          this.filteredAgentArray = data.data;
        })
      .catch((error) => {
                  this.filteredAgentArray = []
                  throw error
      })
      .finally(() => {
          this.isFetchingAgents = false
      })
        ;
    },500),
    MarkReviewComment(commentid)
    {
      const params = [`module=ChatManager`, `c=ReviewThread`, `json=1`].join("&");
      this.$api
        .post(`addonmodules.php?${params}`, {
          entry: commentid,
          action: "ReviewComment",
        })
        .then((response) => {
          if (response.data.data == "success") {
           
            this.$buefy.toast.open({
              container: ".modal-card",
              message: 'Entry marked as seen',
              type: "is-success",
            });
            this.loadReviews()
            this.loadReviewStatus()
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data,
              type: "is-warning",
            });
          }
        });
    },
    sendToReview() {
      if (this.commmentReview.length == 0) {
        this.$buefy.toast.open({
          container: ".modal-card",
          message: "Comment cannot be empty",
          type: "is-danger",
        });
        return;
      }
      this.sendReviewLoadingBtn = true;
      const params = [`module=ChatManager`, `c=ReviewThread`, `json=1`].join(
        "&"
      );
      this.$api
        .post(`addonmodules.php?${params}`, {
          threadid: this.item.id,
          comment: this.commmentReview,
          action: "SaveReview",
        })
        .then((response) => {
          if (response.data == "success") {
            // this.$emit("close")
            //this.loadChats()
      if(this.groupMember == 2)
      {
        this.loadReviews()
      }
      this.loadReviewStatus()
            this.$buefy.toast.open({
              container: ".modal-card",
              message: "Chat sent to review successfuly",
              type: "is-success",
            });
            //this.getTags();
            //this.loadTagsHistory()
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data,
              type: "is-warning",
            });
          }
          this.sendReviewLoadingBtn = false;
        });
    },
    deleteTag(tag) {
      const params = [`module=ChatManager`, `c=Tags`, `json=1`].join("&");
      this.$api
        .post(`addonmodules.php?${params}`, {
          tag: tag.id,
          action: "DeleteTag",
        })
        .then((response) => {
          if (response.data.data == "success") {
            // this.$emit("close")
            //this.loadChats()
            var msg = "";
            if (this.groupMember == 2) msg = "You deleted the tag.";
            else msg = "You proposed the tag deletion.";
            this.$buefy.toast.open({
              container: ".modal-card",
              message: msg ?? "",
              type: "is-success",
            });
            this.getTags();
            this.loadTagsHistory();
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data,
              type: "is-warning",
            });
          }
        });
    },
    approveTag(tag) {
      const params = [`module=ChatManager`, `c=Tags`, `json=1`].join("&");
      this.$api
        .post(`addonmodules.php?${params}`, {
          tag: tag.id,
          action: "ApproveTag",
        })
        .then((response) => {
          if (response.data.data == "success") {
            // this.$emit("close")
            //this.loadChats()
            this.$buefy.toast.open({
              container: ".modal-card",
              message: "You approved the tag",
              type: "is-success",
            });
            this.getTags();
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data,
              type: "is-warning",
            });
          }
        });
    },
    checkOrder() {
      if (!this.selectedOrder) {
        this.OrderStatusField = "is-danger";
        return;
      }
      this.OrderStatusField = null;
      const params = [`module=ChatManager`, `c=Orders`, `json=1`].join("&");
      this.loadingCheckBtn = true;
      this.$api
        .post(`addonmodules.php?${params}`, {
          order: this.selectedOrder,
          a: "CheckOrderID",
          threadid: this.item.id,
        })
        .then((response) => {
          if (response.data == "success") {
            // this.$emit("close")
            //this.loadChats()
            this.$buefy.toast.open({
              container: ".modal-card",
              message: "This Order is new.",
              type: "is-success",
            });
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data,
              type: "is-warning",
            });
          }
          this.loadingCheckBtn = false;
        });
    },
    save() {
      const params = [`module=ChatManager`, `c=Threads`, `json=1`].join("&");
      this.loadingSaveBtn = true;

      var cannotofferReason = this.cannotofferCustom
        ? this.cannotofferCustom
        : this.cannotoffer;
      this.saveLoadingBtn = true;
      this.$api
        .post(`addonmodules.php?${params}`, {
          id: this.item.id,
          name: this.name,
          email: this.email,
          domain: this.domain,
          order: this.selectedOrder,
          notes: this.notes,
          customoffer: cannotofferReason,
          agent: this.agent
        })
        .then((response) => {
          if (response.data == "success") {
            this.$emit("close");
            this.loadChats();
            this.$buefy.toast.open({
              message: "Changes saved",
              type: "is-success",
            });
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data,
              type: "is-warning",
            });
          }
          this.saveLoadingBtn = false;
        });
    },
    addtag() {
      const params = [`module=ChatManager`, `c=Tags`, `json=1`].join("&");
      this.$api
        .post(`addonmodules.php?${params}`, {
          tid: this.item.id,
          tag: this.newtag,
          action: "Add",
        })
        .then((response) => {
          if (response.data == "success") {
            //this.$emit("close")
            //this.loadChats()
            this.getTags();
            var msg = "";
            if (this.groupMember == 2) msg = "Added new tag";
            else msg = "Added to review by supervisor.";
            this.$buefy.toast.open({
              message: msg,
              type: "is-success",
            });
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data,
              type: "is-warning",
            });
          }
        });
    },
    getTags() {
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=Tags&json=1&a=GetTagsSingleThread&threadid=${this.item.id}`
        )
        .then(({ data }) => {
          this.tags = data.data;
          this.HasCustomOfferCheck();
        });
    },
    loadTagsHistory() {
      this.tagsLogLoading = true;
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=TagsHistory&json=1&a=GetTagsLog&threadid=${this.item.id}`
        )
        .then(({ data }) => {
          this.tagslog = data.data;
          this.tagsLogLoading = false;
        });
    },
    HasCustomOfferCheck() {
      if (!this.cannotofferReasons.includes(this.item.customoffer)) {
        this.cannotoffer = "Other";
        this.cannotofferCustom = this.item.customoffer;
      }
      var that = this;
      this.tags.forEach((element) => {
        if (element.tag == "cannot offer") {
          that.HasCustomOffer = true;
          return;
        }
      });
    },
    loadReviewStatus()
    {
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=ReviewThread&json=1&action=GetReviewStatus&threadid=${this.item.id}`
        )
        .then(({ data }) => {
          if(data.result == 'success')
          {
            this.reviewStatus = data.data
          }
          else
          {
             this.$buefy.toast.open({
              container: ".modal-card",
              message: data.msg,
              type: "is-warning",
            });
          }
        });
    },
    loadReviews()
    {
       this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=ReviewThread&json=1&action=GetReviews&threadid=${this.item.id}`
        )
        .then(({ data }) => {
          if(data.result == 'success')
          {
            this.reviewRequests = data.data
          }
          else
          {
             this.$buefy.toast.open({
              container: ".modal-card",
              message: data.msg,
              type: "is-warning",
            });
          }
        });
    }
    // getAsyncData: debounce(function (name) {
    //   if (!name.length) {
    //     this.clients = [];
    //     return;
    //   }
    //   this.isFetchingClients = true;
    //   this.$api
    //     .get(`addonmodules.php?module=ChatManager&c=Clients&json=1&q=${name}`)
    //     .then(({ data }) => {
    //       this.clients = [];
    //       data.data.forEach((item) => this.clients.push(item));
    //     })
    //     .catch((error) => {
    //       this.clients = [];
    //       throw error;
    //     })
    //     .finally(() => {
    //       this.isFetchingClients = false;
    //     });
    // }, 500),
  },
  mounted() {
    this.name = this.item.name ? this.item.name : this.item.customer.name;
    this.email = this.item.email ? this.item.email : this.item.customer.email;
    this.domain = this.item.domain;
    this.selectedOrder = this.item.orderid;
    this.notes = this.item.notes;
    this.agent = this.item.agent
    this.getPermissions().then(() => {
      if (this.groupMember == 2) {
        this.loadTagsHistory()
        this.loadReviews()
      }
      else
      {
        this.loadReviewStatus()
      }
    });
    this.tags = this.item.tags;
    this.cannotoffer = this.item.customoffer;

    this.HasCustomOfferCheck();
  },
  watch: {
    cannotoffer() {
      //this.cannotofferCustom = ''
    },
    //watch selection of client and read service when client is selected
    // selectedClient(val) {
    //   if (val) {
    //     this.$api
    //       .get(
    //         `addonmodules.php?module=ChatManager&c=ClientsServices&json=1&cid=${val.id}`
    //       )
    //       .then(({ data }) => {
    //         this.services = data.data;
    //         // data.data.forEach((item) => this.services.push(item));
    //       })
    //       .catch((error) => {
    //         this.services = [];
    //         throw error;
    //       });
    //   } else {
    //     this.services = [];
    //     this.selectedService = null;
    //   }
    // },
    // selectedService(val) {
    //   if (val) {
    //     this.services.forEach((item) => {
    //       if (item.id == val) this.selectedOrder = item.orderid;
    //       return;
    //     });
    //   }
    // },
  },
  data() {
    return {
      isFetchingAgents: false,
      selectedAgent: '',
      filteredAgentArray: [],
      agent: '',
      reviewStatus: 0,
      reviewRequests: [],
      commmentReview: "",
      tagsLogLoading: false,
      saveLoadingBtn: false,
      sendReviewLoadingBtn: false,
      tags: [],
      order: "",
      clients: [],
      tagslog: [],
      isFetchingClients: false,
      selectedClient: null,
      currentClient: null,
      services: [],
      selectedService: null,
      selectedOrder: null,
      loadingSaveBtn: false,
      newtag: null,
      name: null,
      email: null,
      domain: null,
      notes: "",
      tagsList: [
        "sales",
        "wcb",
        "directsale",
        "convertedsale",
        "notsure",
        "georgistatev",
        "upgrade",
        "upsell",
        "firstlastname",
        "elvira",
        "ivaylo",
        "domain",
        "pushupsell",
        "tegan",
        "cycle",
        "promocode",
        "emiliy",
        "deni",
        "alexp",
        "pushcycle",
        "pending",
        "#blackfriday",
        "custom",
        "phone",
        "#4thjuly",
        "duplicate",
        "vps/ds",
        "cannot offer",
      ],
      cannotofferReasons: [
        "Pricing ( Reseller/Starter Shared)",
        "Accepting Bitcoins/Cryptocurrencies",
        "Django ( on Shared*)",
        ".js (on Shared*)",
        "Python (on Shared*)",
        "Crystal Reports (on Shared*)",
        "Java (on Shared*)",
        "Gambling websites",
        "Root access (shared/cloud*)",
        "IP range/multiple IPs",
        "Unlimited Inodes shared/cloud",
        "Storage size",
        "Unmanaged VPS",
        "Mongo DB",
        "Dedicated IP (shared/cloud)",
        "Over 1 GB database shared/cloud",
        "Mailbox sizes (too small for shared/cloud)",
        "MVC access on Win shared",
        "Other",
      ],
      loadingCheckBtn: false,
      OrderStatusField: undefined,
      HasCustomOffer: false,
      cannotoffer: "",
      cannotofferCustom: "",
      // perPage: 10
    };
  },
};
</script>
<style >
.reviewTabHeader
{
     background: #ffcf76;
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
/* .current
{
  width: 100%;
            overflow: hidden;
            border: 2px solid #296fa8;
            border-radius: 5px;
            padding: 10px;
} */
section.tab-content {
  border-radius: 0.25rem;
  box-shadow: 0 0.5em 1em -0.125em rgb(10 10 10 / 10%),
    0 0 0 1px rgb(10 10 10 / 2%);
  color: #4a4a4a;
  max-width: 100%;
  position: relative;
}
</style>