<template>
  <form action="">
    <div class="modal-card" style="width: 95vw">
      <header class="modal-card-head">
        <p class="modal-card-title">Add Points</p>
        <button type="button" class="delete" @click="$emit('close')" />
      </header>
      <section class="modal-card-body">
        <b-message title="Info" type="is-info" has-icon :closable="false">
          This form can be used to manually add (or remove) points for certain
          agent. To make it correct, fill number of points (if negative, add `-`
          before number, example: -2), date on which it will be counted and
          comment with reason for this operation.
        </b-message>

        <b-field label="Points">
          <b-numberinput step="1" :controls="false" v-model="points" placeholder="Fill points number"></b-numberinput>
        </b-field>

        <b-field label="Agent">
          <b-autocomplete
            v-model="agentTyping"
            ref="autocomplete"
            :data="operatorsFiltered"
            @select="(option) => (agentSelected = option)"
            field="firstname+' '+lastname"
            :custom-formatter="
              (option) => option.firstname + ' ' + option.lastname
            "
             placeholder="Start typing for operator lookup"
          >
            <template slot-scope="props">
              <div class="media">
                {{ props.option.firstname }} {{ props.option.lastname }} ({{
                  props.option.email
                }})
              </div>
            </template>
            <template #empty>No results for {{ agentTyping }}</template>
          </b-autocomplete>
        </b-field>
        <b-field label="Date">
          <b-datepicker
            v-model="date"
            placeholder="Click to select..."
            icon="calendar-today"
            icon-right-clickable
            trap-focus
            position="is-top-right"
            :first-day-of-week="1"
          >
          </b-datepicker>
        </b-field>
        <b-field label="Notes">
          <b-input type="textarea" v-model="comment" placeholder="Optional comment"></b-input>
        </b-field>
      </section>
      <footer class="modal-card-foot">
        <b-button label="Close" @click="$emit('close')" />
        <b-button
          label="Save changes"
          type="is-primary"
          @click="save"
          :loading="loading.saveLoadingBtn"
        />
      </footer>
    </div>
  </form>
</template>
<script>
import { mapState, mapActions } from "vuex";
import { dateMixin } from "../mixins/dateMixin.js";
import memberMixin from "../mixins/memberMixin";
import requestMixin from "../mixins/requestsMixin";
import notificationsMixin from "../mixins/notificationsMixin";

export default {
  name: "AddManualPointsModal",
  mixins: [dateMixin, memberMixin, notificationsMixin, requestMixin],
  components: {},
  computed: {
    ...mapState("operators", ["operators"]),
    operatorsFiltered() {
      return this.operators.filter((option) => {
        return (
          option.firstname
            .toString()
            .toLowerCase()
            .indexOf(this.agentTyping.toLowerCase()) >= 0 ||
          option.lastname
            .toString()
            .toLowerCase()
            .indexOf(this.agentTyping.toLowerCase()) >= 0 ||
          (
            option.firstname.toString().toLowerCase() +
            " " +
            option.lastname.toString().toLowerCase()
          ).indexOf(this.agentTyping.toLowerCase()) >= 0 ||
          option.email
            .toString()
            .toLowerCase()
            .indexOf(this.agentTyping.toLowerCase()) >= 0
        );
      });
    },
  },
  methods: {
    ...mapActions({
      getPermissions: "getPermissions",
      loadOperators: "operators/loadOps",
    }),

    save() {
      if (this.points == 0 || !this.points) {
        this.notifyDanger("You need to set points.");
        return;
      }
      if (!(this.agentSelected && this.agentSelected.id)) {
        this.notifyDanger("You need to set operator.");
        return;
      }
      this.loading.saveLoadingBtn = true;

      const params = this.generateParamsForRequest("Points");
      this.loading.loadingSaveBtn = true;
      this.$api
        .post(`addonmodules.php?${params}`, {
          a: "Add",
          points: this.points,
          operator: this.agentSelected.id,
          comment: this.comment,
          date: this.parseDate(this.date)
        })
        .then((response) => {
          if (response.data.result == "success") {
            
            //this.loadChats();
            this.$buefy.toast.open({
              message: "Changes saved",
              type: "is-success",
            });
            this.$emit('refreshstats')
            this.$emit("close")

          } else {
            this.$buefy.toast.open({
              container: ".modal-card",
              message: response.data.msg,
              type: "is-warning",
            });
          }
          this.loading.saveLoadingBtn = false;
        });
    },
  },
  mounted() {
    this.loadOperators()
     let dateGeneral = new Date();
      this.date = new Date(
        dateGeneral.getUTCFullYear(),
        dateGeneral.getUTCMonth(),
        dateGeneral.getUTCDate()
      );
  },

  data() {
    return {
      points: 0,
      agentTyping: "",
      agentSelected: "",
      comment: "",
      date:null,
      loading: {
        saveLoadingBtn: false,
      },
    };
  },
};
</script>
<style>
.b-numberinput input[type=number] {
  text-align:left !important;
}
</style>
