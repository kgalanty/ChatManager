import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import Buefy from 'buefy'

import chatsLogStore from './chatlogs/store'
import chatcolumnsStore from './chatcolumns/store'
import chatsStore from './chats/store'
import tagsStore from './tags/store'
import OrdersStore from './orders/store'
Vue.use(Vuex)
Vue.use(Buefy)

export default new Vuex.Store({
  state: {
    // chats: [],
    // chatsPage: 1,
    // chatsLoading: true,
    // chatsPerPage: 25,
    // filters: { dateFrom: null, dateTo: null },
    aid: 0,
    groupMember: 0,
    darkstyle: false

  },
  mutations: {
    setGroupMember(state, val) {
      state.groupMember = val.results.perm
      state.aid = val.results.aid
    },
    setDarkstyle(state, val)
    {
      state.darkstyle = Boolean(val)
    }

  },
  actions: {
    switchStyle(context, payload)
    {
      window.localStorage.setItem('darktheme', payload)
      context.commit('setDarkstyle', payload)
    },
    getPermissions(context) {
      return new Promise((resolve, reject) => {
        if(context.state.groupMember > 0 )
        {
          resolve()
          return
        }
        const params = [
          `module=ChatManager`,
          `c=Auth`,
          `json=1`,
          `a=readPermissions`].join("&");
        axios
          .get('addonmodules.php?' + params)
          .then((response) => {
            if (response) {

              context.commit('setGroupMember', response.data)
              resolve();
              return

            }
            reject('No response received')
          }
          )
      });
    },

  },
  modules: {
    chat: chatsStore,
    chatlogs: chatsLogStore,
    chatcolumns: chatcolumnsStore,
    tags: tagsStore,
    orderschats: OrdersStore
  }
})
