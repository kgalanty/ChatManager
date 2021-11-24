<template>
  <form action="">
    <div class="modal-card" style="width: 95vw">
      <header class="modal-card-head">
        <p class="modal-card-title">Delete</p>
        <button type="button" class="delete" @click="$emit('close')" />
      </header>
      <section class="modal-card-body">
        <b-message type="is-danger" has-icon>
          <p>You are about to delete data about order+chat recorded automatically when the customer completed the order. 
          This action cannot be undone. </p>
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
          label="Delete"
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
/* eslint-disable vue/no-unused-components */
import memberMixin from "../mixins/memberMixin";
import notificationsMixin from '../mixins/notificationsMixin';
import requestsMixin from "../mixins/requestsMixin";
export default {
  name: "DeleteOrderDataModal",
  mixins: [memberMixin, requestsMixin, notificationsMixin],
  components: {},
  computed: {},
  methods: {
    DeleteItemConfirm()
    {

    },
    lookupAPI() {
      this.error = "";
      this.resetFieldState('threadid')
      this.proceedBtnLoading = true;
      const params = this.generateParamsForRequest("LiveChat");
      this.$api
        .get(`addonmodules.php?${params}&tid=${this.threadid.value}`)
        .then((data) => {
          this.proceedBtnLoading = false;
          if (data.data.result == "success") {
           
            this.$emit("close");
          } else {

            this.showError('threadid', data.data.msg)
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
