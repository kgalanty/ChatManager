import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import Buefy from 'buefy'

import axios from 'axios'
import moment from 'moment'
Vue.prototype.$api = axios
Vue.use(Buefy,
  {
    defaultContainerElement: '#app'
  })
Vue.config.productionTip = false
Vue.prototype.moment = moment
new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
