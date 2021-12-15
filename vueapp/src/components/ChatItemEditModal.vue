<template>
  <form action="">
    <div class="modal-card" style="width: 95vw">
      <header class="modal-card-head">
        <p class="modal-card-title">Edit chat {{ item.threadid }}</p>
        <button type="button" class="delete" @click="$emit('close')" />
      </header>
      <section class="modal-card-body">
        <b-message
          title="Duplicated Order ID detected"
          type="is-warning"
          has-icon
          :closable="false"
          v-if="
            item.sameorder_count &&
            item.sameorder_count > 1 &&
            !hideMessage &&
            isAdmin()
          "
        >
          This chat has duplicated order ID with
          {{ item.sameorder_count - 1 }} other chats. You need to change it
          manually or confirm it's correct as it is - to close this message and
          remove the chat from 'Pending Review' Table.

          <section>
            <b-button
              style="margin: 5px"
              type="is-info"
              @click="ConfirmDuplicatedChat(item.id)"
            >
              Confirm this chat</b-button
            >
            <b-button
              style="margin: 5px"
              type="is-link"
              @click="ConfirmDuplicatedAllChats(item.orderid)"
              >Confirm all chats with the same order ID</b-button
            >
          </section>
        </b-message>
        <b-tabs type="is-toggle" expanded v-model="activeTab">
          <b-tab-item label="Customer" icon="account-box">
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
            <span style="color: #c3c3c3"
              >Order <b-switch v-model="takeInvoice"></b-switch> Invoice</span
            >
            <b-field label="Invoice ID" v-if="takeInvoice">
              <b-input
                v-model="invoiceid"
                placeholder="Fill invoice number"
                expanded
                type="number"
              ></b-input>
            </b-field>

            <b-field label="Order" v-if="!takeInvoice">
              <b-input
                v-model="selectedOrder"
                placeholder="Fill order number"
                expanded
                type="number"
              ></b-input>

              <b-button
                style="float: left"
                icon-right="magnify"
                type="is-info"
                :loading="loadingCheckBtn"
                @click="checkOrder(false)"
              />
            </b-field>
            <span v-if="orderwaschanged && isAgent()" style="color: purple">
              Your change will be reviewed by supervisor and accepted or
              rejected.
            </span>
            <!-- btable with order suggestions -->
            <b-collapse
              :open="false"
              position="is-bottom"
              v-if="orderchangesuggestions.length > 0"
            >
              <template #trigger="props">
                <a>
                  <b-icon
                    :icon="!props.open ? 'menu-down' : 'menu-up'"
                  ></b-icon>
                  {{ !props.open ? "Pending Order/Invoice Sugestions" : "Hide" }}
                </a>
              </template>
              <b-table
                class="smalltable"
                :data="orderchangesuggestions"
                bordered
                striped
                narrowed
                :total="orderchangesuggestions.length"
                :loading="loading.orderSuggestionTable"
                ><template #empty v-if="orderchangesuggestions == []">
                  <div class="has-text-centered">No entries</div>
                </template>
                <b-table-column
                  field="orderid"
                  label="Suggestion"
                  v-slot="props"
                  width="100"
                  centered
                >
                  {{ props.row.orderid }}
                  <span v-if="props.row.invoice == 1">(Invoice ID)</span>
                </b-table-column>
                <b-table-column
                  field="doer"
                  label="By"
                  v-slot="props"
                  width="100"
                  centered
                >
                  {{ props.row.doer.firstname }} {{ props.row.doer.lastname }}
                </b-table-column>
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
                  label="Status"
                  width="100"
                  centered
                  v-if="isAgent()"
                  >Verification pending
                </b-table-column>
                <b-table-column
                  field="date"
                  label="Actions"
                  width="100"
                  centered
                  v-slot="props"
                  v-if="isAdmin()"
                >
                  <p style="display: inline-block; margin: 2px">
                    <b-tooltip
                      label="By accepting, you will set order ID (or invoice id) and remove other suggestions"
                      ><b-button
                        @click="
                          acceptOrderSuggestion(props.row.id, props.row.invoice)
                        "
                        type="is-link"
                        icon-right="check"
                        size="is-small"
                      ></b-button
                    ></b-tooltip>
                  </p>
                  <p style="display: inline-block">
                    <b-button
                      type="is-danger"
                      @click="
                        declineOrderSuggestion(props.row.id, props.row.invoice)
                      "
                      icon-right="close"
                      size="is-small"
                    ></b-button>
                  </p>
                </b-table-column>
              </b-table>
            </b-collapse>

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
            <b-field label="Agent" v-if="isAdmin()">
              <b-autocomplete
                v-model="agent"
                ref="autocomplete"
                :data="filteredAgentArray"
                @select="(option) => (selectedAgent = option)"
                field="firstname+' '+lastname"
                @typing="getAgents"
                :loading="loading.isFetchingAgents"
                :custom-formatter="
                  (option) => option.firstname + ' ' + option.lastname
                "
              >
                <template slot-scope="props">
                  <div class="media">
                    {{ props.option.firstname }} {{ props.option.lastname }} ({{
                      props.option.email
                    }})
                  </div>
                </template>
                <template #empty>No results for {{ agent }}</template>
              </b-autocomplete>
            </b-field>
            <b-field label="Notes">
              <b-input type="textarea" v-model="notes"></b-input>
            </b-field>
          </b-tab-item>
          <b-tab-item label="Tags" icon="tag-multiple">
            <b-field label="Tags">
              <b-table
                class="modaltable"
                :data="tags"
                bordered
                striped
                narrowed
              >
                <template #empty>There is no tag here yet. </template>
                <b-table-column field="date" label="Tag" v-slot="props">
                  <b-tag
                    :type="
                      props.row.approved == 1
                        ? 'is-warning'
                        : 'is-warning is-light'
                    "
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
                    <b-tooltip
                      label="Keep the tag & delete the proposal"
                      position="is-left"
                      v-if="isAdmin() && props.row.proposed_deletion == 1"
                    >
                      <b-button
                        icon-right="check"
                        type="is-success"
                        style="font-size: 0.8rem !important"
                        @click="undoProposeDeletion(props.row)"
                      />
                    </b-tooltip>
                    <b-button
                      icon-right="check"
                      type="is-success"
                      style="font-size: 0.8rem !important"
                      @click="approveTag(props.row)"
                      v-if="isAdmin() && props.row.approved == 0" />
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
                :disabled="loading.addtagBtnLoading"
              >
                <option :value="tag" :key="i" v-for="(tag, i) in tagsList">
                  {{ tag }}
                </option>
                <option value="-addnew-">-Add New-</option> </b-select
              ><b-button
                style="float: left"
                icon-right="plus"
                type="is-info"
                @click="addtag"
                :loading="loading.addtagBtnLoading"
                >Add Tag</b-button
              >
            </b-field>

            <b-collapse
              :open="false"
              position="is-top"
              animation="slide"
              v-if="isAdmin()"
            >
              <template #trigger="props">
                <a>
                  <h2 class="label">
                    <b-icon
                      :icon="!props.open ? 'menu-down' : 'menu-up'"
                    ></b-icon>
                    Tag Log
                  </h2>
                </a>
              </template>

              <b-field label="">
                <b-table
                  class="modaltable"
                  :data="tagslog"
                  narrowed
                  :per-page="5"
                  :loading="loading.tagsLogLoading"
                >
                  <template #empty>
                    <div
                      class="has-text-centered"
                      v-if="!loading.tagsLogLoading"
                    >
                      No entries
                    </div>
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
            <template #header>
              <b-icon icon="message-draw"></b-icon>
              <span>
                Review
                <b-tag rounded type="is-info">
                  {{ reviewRequests.length }}
                </b-tag>
              </span>
            </template>
            <b-message type="is-warning" has-icon v-if="reviewStatus == 1"
              >This thread has pending reviews.</b-message
            >
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
                  :loading="loading.sendReviewLoadingBtn"
                  >Send to review</b-button
                >
              </div>
            </div>

            <b-table
              class="modaltable"
              :data="reviewRequests"
              narrowed
              :per-page="5"
            >
              <template #empty>
                <div class="has-text-centered">No entries</div>
              </template>
              <b-table-column
                field="pending"
                label="Seen"
                v-slot="props"
                width="50"
              >
                <b-icon
                  icon="close"
                  v-if="props.row.pending == 1"
                  size="is-small"
                  type="is-danger"
                ></b-icon>
                <b-icon
                  icon="check"
                  v-if="props.row.pending == 0"
                  size="is-small"
                  type="is-success"
                ></b-icon>
              </b-table-column>
              <b-table-column
                field="date"
                label="Performed by"
                v-slot="props"
                width="200"
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
                {{ parseDateTimeFromUTCtoLocal(props.row.created_at) }}
              </b-table-column>
              <b-table-column
                field="pending"
                label="Actions"
                v-slot="props"
                :visible="isAdmin()"
              >
                <b-button
                  type="is-success"
                  size="is-small"
                  icon-right="check"
                  v-if="props.row.pending == 1"
                  @click="MarkReviewComment(props.row.id)"
                ></b-button>
              </b-table-column>
            </b-table>
          </b-tab-item>
          <b-tab-item>
            <template #header>
              <b-icon icon="list-status"></b-icon>
              <span> Logs </span>
            </template>
            <ChatItemLogs :threadid="item.id" />
          </b-tab-item>
        </b-tabs>
      </section>
      <footer class="modal-card-foot">
        <b-button label="Close" @click="$emit('close')" />
        <b-button
          label="Save changes"
          type="is-primary"
          @click="save"
          :loading="loading.saveLoadingBtn"
        />
        <b-button
          label="Delete"
          type="is-danger"
          style="margin-left: auto; order: 2"
          @click="delThread"
          v-if="isAdmin()"
        />
      </footer>
    </div>
  </form>
</template>
<script>
import ChatItemLogs from "./ChatItemLogs.vue";
import debounce from "lodash/debounce";
import { mapActions } from "vuex";
import { dateMixin } from "../mixins/dateMixin.js";
import { tagsMixin } from "../mixins/tagsMixin";
import memberMixin from "../mixins/memberMixin";
import requestMixin from "../mixins/requestsMixin";
import notificationsMixin from "../mixins/notificationsMixin";
import { pendingReviewActionsMixin } from "../mixins/pendingReviewActionsMixin";
import { cannotofferMixin } from "../mixins/cannotofferMixin";

export default {
  name: "ChatItemEditModal",
  props: ["item"],
  mixins: [
    dateMixin,
    tagsMixin,
    memberMixin,
    notificationsMixin,
    requestMixin,
    pendingReviewActionsMixin,
    cannotofferMixin,
  ],
  components: { ChatItemLogs },
  computed: {
    reviewTabHeader() {
      if (this.reviewStatus == 1) {
        return "reviewTabHeader";
      }
      return "";
    },
    tagsList() {
      return this.$store.state.tags.tags;
    },
    showinvoiceinsteadoforder() {
      return (
        this.item.tags.find((e) => {
          if (e.tag == "upgrade" && e.approved == 1) {
            return true;
          }
        }) || false
      );
    },
  },
  methods: {
    ...mapActions({
      loadChats: "chat/loadChats",
      getPermissions: "getPermissions",
      loadLogs: "chatlogs/loadLogs",
      loadTags: "tags/loadTags",
      clearLogs: "chatlogs/clearLogs",
    }),
    delThread() {
      this.$buefy.dialog.prompt({
        message: `You are about to delete this chat and all related data. This action cannot be undone (you can later reimport this chat manually). Are you sure you wish to proceed?`,
        title: "Delete confirmation",
        hasIcon: true,
        icon: "alert-circle",
        type: "is-danger",
        inputAttrs: {
          placeholder: "type YES here",
          maxlength: 3,
        },
        trapFocus: true,
        onConfirm: (value) => {
          if (value === "YES") {
            const params = this.generateParamsForRequest("ChatTable", []);
            this.$api
              .post(`addonmodules.php?${params}`, {
                a: "DelThread",
                tid: this.item.id,
              })
              .then(() => {
                this.$emit("close");
                this.notifySuccess("Chat is deleted");
              })
              .catch((error) => {
                throw error;
              })
              .finally(() => {});
          } else {
            this.notifyDanger('To delete the chat, you had to type "YES"');
          }
        },
      });
    },
    getAgents: debounce(function (name) {
      this.loading.isFetchingAgents = true;
      const params = this.generateParamsForRequest("Agents", [
        "a=GetAgentsList",
      ]);
      this.$api
        .get(`addonmodules.php?${params}&q=${name}`)
        .then(({ data }) => {
          this.filteredAgentArray = data.data;
        })
        .catch((error) => {
          this.filteredAgentArray = [];
          throw error;
        })
        .finally(() => {
          this.loading.isFetchingAgents = false;
        });
    }, 500),
    acceptOrderSuggestion(suggestionid, isInvoice) {
     
      this.loading.orderSuggestionTable = true;
      const params = this.generateParamsForRequest("Orders");
      this.$api
        .post(`addonmodules.php?${params}`, {
          entry: suggestionid,
          a: "AcceptSuggestion",
        })
        .then((response) => {
          this.loading.orderSuggestionTable = false;
          if (response.data.result == "success") {
            if (isInvoice == 1) {
              this.invoiceid = response.data.orderid;
              this.takeInvoice = true;
            } else {
              this.selectedOrder = response.data.orderid;
              this.takeInvoice = false;
            }
            this.loadOrdersSuggestions();
            this.notifySuccess(
              (isInvoice == 1 ? "Invoice" : "Order") + " suggestion approved"
            );
          } else {
            this.notifyWarning(response.data);
          }
        });
    },
    declineOrderSuggestion(suggestionid, isInvoice) {
      this.loading.orderSuggestionTable = true;
      const params = this.generateParamsForRequest("Orders");
      this.$api
        .post(`addonmodules.php?${params}`, {
          entry: suggestionid,
          a: "DeclineSuggestion",
        })
        .then((response) => {
          this.loading.orderSuggestionTable = false;
          if (response.data == "success") {
            this.loadOrdersSuggestions();
            this.notifySuccess(
              (isInvoice == 1 ? "Invoice" : "Order") + " suggestion rejected"
            );
          } else {
            this.notifyWarning(response.data);
          }
        });
    },
    MarkReviewComment(commentid) {
      const params = this.generateParamsForRequest("ReviewThread");
      this.$api
        .post(`addonmodules.php?${params}`, {
          entry: commentid,
          action: "ReviewComment",
          threadid: this.item.id,
        })
        .then((response) => {
          if (response.data.data == "success") {
            this.notifySuccess("Entry marked as seen");
            this.loadReviews();
            this.loadReviewStatus();
          } else {
            this.notifyWarning(response.data);
          }
        });
    },
    sendToReview() {
      if (this.commmentReview.length == 0) {
        this.notifyDanger("Comment cannot be empty");
        return;
      }
      this.loading.sendReviewLoadingBtn = true;
      const params = this.generateParamsForRequest("ReviewThread");
      this.$api
        .post(`addonmodules.php?${params}`, {
          threadid: this.item.id,
          comment: this.commmentReview,
          action: "SaveReview",
        })
        .then((response) => {
          if (response.data == "success") {
            if (this.isAdmin()) {
              this.loadReviews();
            }
            this.loadReviewStatus();
            this.notifySuccess("Comment sent to review successfuly");
          } else {
            this.notifyWarning(response.data);
          }
          this.loading.sendReviewLoadingBtn = false;
        });
    },
    deleteTag(tag) {
      const params = this.generateParamsForRequest("Tags");
      this.$api
        .post(`addonmodules.php?${params}`, {
          tag: tag.id,
          action: "DeleteTag",
        })
        .then((response) => {
          if (response.data.data == "success") {
            var msg = "";
            if (this.isAdmin()) msg = "Tag has been deleted successfuly.";
            else msg = "You proposed the tag to be deleted.";
            this.notifySuccess(msg);
            this.getTags();
            this.loadTagsHistory();
            this.HasCustomOfferCheck();
          } else {
            this.notifyWarning(response.data);
          }
        });
    },
    undoProposeDeletion(tag) {
      const params = this.generateParamsForRequest("Tags");
      this.$api
        .post(`addonmodules.php?${params}`, {
          tag: tag.id,
          action: "undoProposeDeletion",
        })
        .then((response) => {
          if (response.data.data == "success") {
            this.notifySuccess("You declined deletion of tag");
            this.getTags();
            this.loadTagsHistory();
          } else {
            this.notifyWarning(response.data);
          }
        });
    },
    approveTag(tag) {
      const params = this.generateParamsForRequest("Tags");

      this.$api
        .post(`addonmodules.php?${params}`, {
          tag: tag.id,
          action: "ApproveTag",
        })
        .then((response) => {
          if (response.data.data == "success") {
            this.notifySuccess("You approved the tag");
            this.getTags();
          } else {
            this.notifyWarning(response.data);
          }
        });
    },
    checkOrder(onlyreturn = false) {
      if (!this.selectedOrder) {
        // this.OrderStatusField = "is-danger";
        return true;
      }
      // this.OrderStatusField = null;
      const params = this.generateParamsForRequest("Orders");
      this.loadingCheckBtn = true;
      return new Promise((resolve) => {
        this.$api
          .post(`addonmodules.php?${params}`, {
            order: parseInt(this.selectedOrder),
            a: "CheckOrderID",
            threadid: this.item.id,
          })
          .then((response) => {
            if (response.data == "success") {
           
              if (!onlyreturn) {
                this.notifySuccess("This Order is new.");
              }
              this.loadingCheckBtn = false;
              resolve("success");
              return 1;
            } else {
              if (!onlyreturn) {
                this.notifyWarning(response.data);
              }
              resolve(response.data);
              this.loadingCheckBtn = false;
              return 0;
            }
          });
      });
    },
    save() {
      this.loading.saveLoadingBtn = true;
      // if (this.selectedOrder && this.orderwaschanged) {
      //   const checkorder = await this.checkOrder(true);
      //   if (checkorder != "success") {
      //     this.notifyWarning(
      //       "This order id is already assigned to another thread."
      //     );
      //     // this.$buefy.toast.open({
      //     //   container: ".modal-card",
      //     //   message: "This order id is already assigned to another thread.",
      //     //   type: "is-warning",
      //     // });
      //     this.loading.saveLoadingBtn = false;
      //     return;
      //   }
      // }
      const params = this.generateParamsForRequest("Threads");
      this.loading.loadingSaveBtn = true;

      var cannotofferReason = this.cannotofferCustom
        ? this.cannotofferCustom
        : this.cannotoffer;
      let newagent = this.selectedAgent ? this.selectedAgent.id : "";
      this.$api
        .post(`addonmodules.php?${params}`, {
          id: this.item.id,
          name: this.name,
          email: this.email,
          domain: this.domain,
          order: this.selectedOrder,
          invoiceid: this.invoiceid,
          takeinvoice: this.takeInvoice,
          notes: this.notes,
          customoffer: cannotofferReason,
          agent: newagent,
        })
        .then((response) => {
          if (response.data.result == "success") {
            this.$emit("close");
            this.loadChats();
            this.$buefy.toast.open({
              message: "Changes saved",
              type: "is-success",
            });
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data.msg,
              type: "is-warning",
            });
            if (response.data.orderid) {
              this.selectedOrder = response.data.orderid;
            }
          }
          this.loading.saveLoadingBtn = false;
        });
    },
    addtag() {
      if (this.newtag == "" || this.newtag == null) {
        this.notifyWarning("Select a tag to add.");
        return;
      }
      this.loading.addtagBtnLoading = true;
      const params = this.generateParamsForRequest("Tags");
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
            if (this.isAdmin()) msg = "Added new tag";
            else msg = "Added to review by supervisor.";
            this.notifySuccess(msg);
            this.loadTagsHistory();
          } else {
            this.notifyWarning(response.data);
          }
          this.loading.addtagBtnLoading = false;
        });
    },
    getTags() {
      const params = this.generateParamsForRequest("Tags", [`a=GetTagsSingleThread`,`threadid=${this.item.id}`]);
      this.$api
        .get(
          `addonmodules.php?${params}`
        )
        .then(({ data }) => {
          this.tags = data.data;
          this.HasCustomOfferCheck();
        });
    },
    loadTagsHistory() {
      this.loading.tagsLogLoading = true;
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=TagsHistory&json=1&a=GetTagsLog&threadid=${this.item.id}`
        )
        .then(({ data }) => {
          this.tagslog = data.data;
          this.loading.tagsLogLoading = false;
        });
    },
    loadOrdersSuggestions() {
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=Orders&json=1&tid=${this.item.id}`
        )
        .then(({ data }) => {
          this.orderchangesuggestions = data.data;
        });
    },
    HasCustomOfferCheck() {
      //sprawdzic czemu po usunieciu taga nadal wyswietla sie pole cannot offer z listą
      if (
        this.item.customoffer &&
        !this.cannotofferReasons.includes(this.item.customoffer)
      ) {
        this.cannotoffer = "Other";
        this.cannotofferCustom = this.item.customoffer;
      }
      var that = this;
      this.HasCustomOffer = false;
      this.tags.forEach((element) => {
        if (element.tag == "cannot offer") {
          that.HasCustomOffer = true;
          return;
        }
      });
    },
    loadReviewStatus() {
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=ReviewThread&json=1&action=GetReviewStatus&threadid=${this.item.id}`
        )
        .then(({ data }) => {
          if (data.result == "success") {
            this.reviewStatus = data.data;
          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: data.msg,
              type: "is-warning",
            });
          }
        });
    },
    loadReviews() {
      this.$api
        .get(
          `addonmodules.php?module=ChatManager&c=ReviewThread&json=1&action=GetReviews&threadid=${this.item.id}`
        )
        .then(({ data }) => {
          if (data.result == "success") {
            this.reviewRequests = data.data;
          } else {
            this.notifyWarning(data.msg);
          }
        });
    },
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
    this.selectedOrder =
      this.item.orderid != 0 && this.item.orderid != ""
        ? this.item.orderid
        : "";
    this.notes = this.item.notes;
    this.agent = this.item.agentdata
      ? this.item.agentdata?.firstname + " " + this.item.agentdata?.lastname
      : "";
    this.invoiceid = this.item.invoiceid;
    if (this.invoiceid) {
      this.takeInvoice = true;
    }
    this.tags = this.item.tags;
    this.cannotoffer = this.item.customoffer;
    this.orderchangesuggestions = [];
    this.loadOrdersSuggestions();
    this.HasCustomOfferCheck();
    // this.getPermissions().then(() => {
    // if (this.isAdmin()) {
    // this.loadTagsHistory();
    this.loadReviews();
    // }
    this.loadReviewStatus();
    //});
    //clear Logs tab to prevent showing logs from previously loaded thread to show when new logs are being loaded
    this.clearLogs();
    this.$nextTick(() => {
      this.WatchOrder = true;
    });
  },
  watch: {
    cannotoffer() {
      //this.cannotofferCustom = ''
    },
    activeTab(val) {
      if (val === 3) {
        //if tab is switched to Logs...
        this.loadLogs({ itemid: this.item.id });
      }
      if (val == 1) {
        this.loadTags();
        if (this.isAdmin()) {
          this.loadTagsHistory();
        }
      }
      if (val == 2) {
        this.loadReviews();
        if (this.reviewStatus < 0) this.loadReviewStatus();
      }
    },
    selectedOrder(prev, val) {
      if (this.WatchOrder && prev != val) {
        this.orderwaschanged = true;
      }
      // console.log( val)
      // console.log(this.WatchOrder)
    },
    newtag(val) {
      if (val == "-addnew-") {
        this.newtag = "";
        this.$buefy.dialog.prompt({
          message: `Insert new tag`,
          inputAttrs: {
            placeholder: "",
            maxlength: 20,
          },
          trapFocus: true,
          onConfirm: (value) => {
            this.newtag = value;
            this.addtag();
          },
        });
      }
    },
  },
  data() {
    return {
      loading: {
        addtagBtnLoading: false,
        tagsLogLoading: false,
        saveLoadingBtn: false,
        sendReviewLoadingBtn: false,
        loadingSaveBtn: false,
        isFetchingAgents: false,
        orderSuggestionTable: false,
      },
      takeInvoice: false,
      activeTab: 0,
      selectedAgent: "",
      filteredAgentArray: [],
      agent: "",
      reviewStatus: -1,
      reviewRequests: [],
      commmentReview: "",
      tags: [],
      order: "",
      invoiceid: "",
      clients: [],
      tagslog: [],
      selectedClient: null,
      currentClient: null,
      services: [],
      selectedService: null,
      selectedOrder: null,
      orderwaschanged: false,
      WatchOrder: false,
      newtag: null,
      name: null,
      email: null,
      domain: null,
      notes: "",

      loadingCheckBtn: false,
      // OrderStatusField: undefined,
      HasCustomOffer: false,
      cannotoffer: "",
      cannotofferCustom: "",
      orderchangesuggestions: [],
      // perPage: 10
    };
  },
};
</script>
<style>
.message.is-warning {
  background-color: #ffefac;
}
.reviewTabHeader {
  background: #ffcf76;
}
.smalltable {
  border: 1px solid black;
  font-size: 0.75em;
}
.modaltable {
  border: 1px solid black;
}
.smalltable thead {
  background: rgb(255, 168, 227);
  background: linear-gradient(
    180deg,
    rgba(255, 168, 227, 1) 0%,
    rgba(166, 163, 251, 1) 0%
  );
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
