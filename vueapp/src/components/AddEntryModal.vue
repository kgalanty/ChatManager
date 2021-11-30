<template>
  <form action="">
    <div class="modal-card" style="width: 95vw">
      <header class="modal-card-head">
        <p class="modal-card-title">Add chat</p>
        <button type="button" class="delete" @click="$emit('close')" />
      </header>
      <section class="modal-card-body">
        <b-message type="is-info" has-icon>
          Insert chat ID. We will read it from LiveChat. You will then be able
          to review and edit all data.
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

        <b-field label="Thread ID" :type="threadid.type"
            :message="threadid.msg">
          <b-input
            v-model="threadid.value"
            placeholder="Fill Thread ID. This cannot be edited later."
          
          ></b-input>
        </b-field>
      </section>
      <footer class="modal-card-foot">
        <b-button label="Close" @click="$emit('close')" />
        <b-button
          label="Proceed"
          type="is-primary"
          icon-right="content-save"
          @click="lookupAPI"
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
  name: "AddEntryModal",
  mixins: [memberMixin, requestsMixin, notificationsMixin],
  components: {},
  computed: {},
  methods: {
    emitEvent(item) {
      this.$emit("runedition", item);
    },
    resetFieldState(field)
    {
      this[field].type = ''
      this[field].msg = ''
      
    },
    showError(field, msg)
    {
      this[field].type = 'is-danger'
      this[field].msg = msg
      this[field].value = ''
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
            this.$emit("runedition", data.data.data);
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
      threadid:
      {
        type: '',
        msg: '',
        value: ''
      },
      error: "",
      proceedBtnLoading: false,
    };
  },
};
</script>
<style >
.reviewTabHeader {
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