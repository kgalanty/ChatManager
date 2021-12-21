<template>
  <form action="">
    <div class="modal-card" style="width: 95vw">
      <header class="modal-card-head">
        <p class="modal-card-title">Delete</p>
        <button type="button" class="delete" @click="$emit('close')" />
      </header>
      <section class="modal-card-body">
        <b-message type="is-danger" has-icon>
          <p>
            You are about to delete data about order+chat recorded automatically
            when the customer completed the order. This action cannot be undone.
          </p>
          <p>Are you sure?</p>
        </b-message>
        <b-notification
          v-if="error"
          type="is-danger"
          has-icon
          :closable="false"
          role="alert"
        >
          {{ error }}
        </b-notification>
      </section>
      <footer class="modal-card-foot">
        <b-button label="Close" @click="$emit('close')" />
        <b-button
          label="Yes, delete it"
          type="is-danger"
          icon-right="delete"
          @click="DeleteItemConfirm"
          :loading="proceedBtnLoading"
        />
      </footer>
    </div>
  </form>
</template>
<script>
import { mapActions } from "vuex";
/* eslint-disable vue/no-unused-components */
import memberMixin from "../mixins/memberMixin";
import notificationsMixin from "../mixins/notificationsMixin";
import requestsMixin from "../mixins/requestsMixin";
export default {
  name: "DeleteOrderDataModal",
  mixins: [memberMixin, requestsMixin, notificationsMixin],
  components: {},
  props:
  {
    id:
    {
      type: Number,
      required: true
    }
  },
  computed: {},
  methods: {
    ...mapActions("orderschats", ["loadOrders"]),
    DeleteItemConfirm() {
      this.proceedBtnLoading = true;
      const params = this.generateParamsForRequest("OrdersChats");
      this.$api
        .post(`addonmodules.php?${params}`, {
          id: this.id,
        })
        .then((data) => {
          this.proceedBtnLoading = false;
          if (data.data.result == "success") {
            this.loadOrders();
            this.$emit("close");
            this.notifySuccess("Entry successfuly deleted");
          } else {
            this.showError("threadid", data.data.msg);
            //this.error = data.data.msg;
          }
        });
    },
  },
  mounted() {
    this.error = "";
  },
  data() {
    return {
      error: "",
      proceedBtnLoading: false,
    };
  },
};
</script>
