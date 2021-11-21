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
    </div>
    <router-view />
    <Footer />
  </div>
</template>

<script>
import Footer from "./components/Footer";
import { mapActions } from "vuex";
export default {
  name: "App",
  components: {
    Footer,
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

<style>

.lighttheme {
  background-color: rgb(204, 205, 223);
}
#app * {
  font-size: 0.8rem;
}
@keyframes roundtime {
  to {
    /* More performant than `width` */
    transform: scaleX(0);
  }
}
#switcher {
  float: right;
}
.notification:after {
  content: "";
  display: block;
  height: 3px;
  width: 100%;
  z-index: 1;
  position: absolute;
  left: 0;
  bottom: 0px;
  transform-origin: left center;
  animation: roundtime calc(5s) linear forwards;
}
.notification.is-success:after {
  background: white;
}
.notification.is-danger:after {
  background: rgb(255, 255, 255);
}
.notification.is-warning:after {
  background: black;
}
[v-cloak] > * {
  display: none;
}
[v-cloak]::before {
  content: "loading…";
}
@font-face {
  font-family: "Manrope";
  font-display: swap;
  src: url(fonts/Manrope-Regular.8ae23f32.woff2) format("woff2"),
    url(fonts/Manrope-Regular.871132a5.woff) format("woff");
  font-weight: 400;
  font-style: normal;
}
@font-face {
  font-family: "Manrope";
  font-display: swap;
  src: url(fonts/Manrope-Medium.5db70385.woff2) format("woff2"),
    url(fonts/Manrope-Medium.e59358f6.woff) format("woff");
  font-weight: 500;
  font-style: normal;
}
@font-face {
  font-family: "Manrope";
  font-display: swap;
  src: url(fonts/Manrope-Bold.09745328.woff2) format("woff2"),
    url(fonts/Manrope-Bold.ec201dfb.woff) format("woff");
  font-weight: 700;
  font-style: normal;
}
@font-face {
  font-family: "Manrope";
  font-display: swap;
  src: url(fonts/Manrope-ExtraBold.6fdca7c1.woff2) format("woff2"),
    url(fonts/Manrope-ExtraBold.014a33bb.woff) format("woff");
  font-weight: 800;
  font-style: normal;
}
.pagination-list a {
  background: white;
}
.pagination-link {
  background: white;
}
body {
  font-family: "Manrope", arial, sans-serif;
}
</style>
<style scoped>
#app .hover a {
  text-decoration: none;
  position: relative;
  margin: 0 3px;
  display: inline-block;
  padding: 15px;
}
#app .hover a:hover {
  background-color: rgb(225, 225, 255);
  border-radius: 5px;
}
#app .hover a::after {
  position: absolute;
  content: "";
  height: 0.05em;
  top: 100%;
  background: rgb(26, 77, 128);
  z-index: 100;
  left: 0;
  right: 0;
  transform: scaleX(0);
  transition: transform 0.3s cubic-bezier(0.95, 0.05, 0.795, 0.035);
}

#app .hover a:hover::after {
  transition-timing-function: cubic-bezier(0.19, 1, 0.22, 1);
  transform: scaleX(1);
}
#app {
  /* font-family: Avenir, Helvetica, Arial, sans-serif; */
  font-family: "Manrope", arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  height: auto;
  min-height: 100vh;
}

#app #nav a {
  font-weight: bold;
  color: #2c3e50;
  background-color: rgb(236, 233, 255);
  border-radius: 5px;
}

#nav a.router-link-exact-active {
  color: #42b983;
}
</style>
<style lang="scss">
.lighttheme {
  .panel-heading {
    padding-left: 0px;
    margin-bottom: 5px;
    color: white;
    background: #7465b6;
  }
  .is-latefollowup {
    background: rgb(252, 186, 186) !important;
  }
  .is-reviewpending {
    background-color: #ffffff;
    background-size: 50px 50px;
    background-image: repeating-linear-gradient(
      45deg,
      #ffdfdf 0,
      #ffdfdf 5px,
      #ffffff 0,
      #ffffff 50%
    );
  }
  .is-lackorder {
    background: rgb(255, 255, 255) !important;
    background: linear-gradient(
      180deg,
      rgba(255, 255, 255, 1) 0%,
      rgba(250, 138, 0, 1) 100%
    ) !important;
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
  .is-upgrade {
    background: rgb(204, 255, 215) !important;
  }
  .is-upgradeinvoice {
    background: rgb(204, 255, 215) !important;
  }
    #statstable {
      background: #acc6ff;
      color: white;
      padding: 5px;
      border-radius: 5px;
    }
}
</style>
<style lang="scss">
.darktheme {
  .statsfilters .tile .label 
  {
    background-color: rgb(95, 89, 148) !important;
  }
      #statstable {
      background: #3c4861;
      color: white;
      padding: 5px;
      border-radius: 5px;
    }
  .footerclass
  {
    color:rgb(180, 180, 180);
  }
  .footerclass a 
  {
    color: grey;
  }
    .footerclass a:hover
  {
    color: rgb(213, 213, 213);
  }
  .datepicker-cell:hover
  {
     background: rgb(98, 70, 143) !important;
  }
  .stats-sticky-column
  {
        background: #b1871b !important;
  }
  #pendingchatlisttable
  {
    background: rgb(163, 53, 75) !important;
  }
  .datepicker-cell
  {
    color: rgb(170, 157, 157) !important;
  }
  .datepicker-header > .datepicker-cell
  {
    color: black !important;
    font-weight: bold;
  }
  .is-selectable
  {
    color: white !important;
  }
  .has-text-primary {
    color:white !important;
  }
  .tag:not(body).is-warning 
  {
    background-color: rgb(95, 88, 107);
    color:white;
  }
  .panel-heading {
    padding-left: 0px;
    margin-bottom: 5px;
    color: white;
    background-color: #544f64;
  }
  .modal-card,
  .modal-card-head,
  .modal-card-body,
  .label,
  .modal-card-title,
  .modal-card-foot {
    background-color: #3f3f3f !important;
    color: rgb(197, 197, 197) !important;
  }
  .b-tabs a {
    color: rgb(126, 126, 126) !important;
  }
  .b-tabs .is-active a {
    color: rgb(240, 240, 240) !important;
  }

  .tabs.is-toggle a:hover {
    color: rgb(255, 255, 255) !important;
    background-color: #4d4d4d;
  }
  //colored rows
  .is-latefollowup {
    background: rgb(167, 77, 77) !important;
  }
  .is-reviewpending {
    background-color: #ffffff;
    background-size: 50px 50px;
    background-image: repeating-linear-gradient(
      45deg,
      #ffdfdf 0,
      #ffdfdf 5px,
      #ffffff 0,
      #ffffff 50%
    );
  }
  .is-lackorder {
    background: rgb(255, 255, 255) !important;
    background: linear-gradient(
      180deg,
      rgba(255, 255, 255, 1) 0%,
      rgba(250, 138, 0, 1) 100%
    ) !important;
  }
  .is-cannotoffer {
    background: rgb(103, 104, 40) !important;
  }
  .is-duplicate {
    background: #787878 !important;
  }
  .is-directsale {
    background: rgb(15, 150, 37) !important;
    color: rgb(182, 182, 182);
  }
  .is-upgrade {
    background: rgb(44, 100, 56) !important;
  }
  .is-upgradeinvoice {
    background: rgb(102, 157, 120) !important;
  }
  //misc
  #nav > a {
    background-color: rgb(144, 107, 187) !important;
    color: white !important;
  }
  #nav > a:hover {
    background-color: rgb(84, 59, 112) !important;
    color: rgb(184, 184, 184) !important;
  }
  background-color: rgb(53, 54, 78) !important;
  .checkbox {
    color: white;
  }
  .checkbox:hover {
    color: rgb(181, 181, 181);
  }
  #switcher {
    color: white;
  }
  .table tr {
    background-color: rgb(53, 54, 78);
    color: white;
  }
  thead th {
    background-color: rgb(105, 106, 153);
    color: rgb(218, 218, 218) !important;
  }
  .button.is-primary.is-outlined {
    border-color: rgb(215, 196, 255);
    border-width: 2px;
    color: white;
  }
  .select select,
  .taginput .taginput-container.is-focusable,
  .textarea,
  .input {
    background-color: #3f3f3f;
    border-color: #cacaca;
    border-radius: 4px;
    color: #c5c5c5;
    -webkit-box-shadow: inset 0 0.0625em 0.125em rgb(100, 100, 100 / 5%);
    box-shadow: inset 0 0.0625em 0.125em rgb(10, 10, 10 / 5%);
  }
  .select select option {
    color: #c5c5c5;
  }
  ::placeholder {
    /* Chrome, Firefox, Opera, Safari 10.1+ */
    color: rgb(153, 153, 153) !important;
    opacity: 1; /* Firefox */
  }

  :-ms-input-placeholder {
    /* Internet Explorer 10-11 */
    color: rgb(153, 153, 153) !important;
  }

  ::-ms-input-placeholder {
    /* Microsoft Edge */
    color: rgb(153, 153, 153) !important;
  }
  .datepicker .dropdown-content {
    background-color: #464664;
  }
  .statsfilters
  {
    background-color:rgb(104, 106, 165);
  }

}
//   html {
//   background: #35397e !important;
// }
</style>