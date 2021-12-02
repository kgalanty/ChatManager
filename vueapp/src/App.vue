<template>
  <div
    id="app"
    v-cloak
    :class="{ darktheme: darktheme, lighttheme: !darktheme }"
  >
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@mdi/font@5.8.55/css/materialdesignicons.min.css"
    />
    <!-- <div id="switcher">
      <b-icon icon="weather-sunny"></b-icon>
      <b-switch
        outlined
        type="is-light"
        passive-type="is-black"
        @input="switchStyle"
        v-model="darktheme"
      ></b-switch>
      <b-icon icon="weather-night"></b-icon>
    </div> -->
    <div id="nav" class="hover">
      <router-link to="/">Chats</router-link>
      <router-link to="/stats">Statistics</router-link>
      <router-link to="/orders" v-if="isAdmin()">Orders</router-link>
      <router-link to="/logs" v-if="isAdmin()">Logs</router-link>
    </div>
    <router-view />
    <StaffOnline />
    <Footer />
  </div>
</template>

<script>
import Footer from "./components/Footer";
import { mapActions } from "vuex";
import '@/assets/scss/light.scss'
import '@/assets/scss/dark.scss'
import '@/assets/scss/global.scss'
import memberMixin from './mixins/memberMixin';
import StaffOnline from '@/components/StaffOnline'
export default {
  name: "App",
   mixins: [
    memberMixin,
  ],
  components: {
    Footer,
    StaffOnline
  },
  data() {
    return {
      darktheme: false,
    };
  },
  methods: {
    ...mapActions(["switchStyle"]),
  },
  mounted() {
    if (window.localStorage.getItem("darktheme") == 'true') {
       this.darktheme = true
       this.switchStyle(this.darktheme)
    }
    //this.points = this.calculatePointsFromTags(this.tags, this.invoiceStatus)
  },
};
</script>
