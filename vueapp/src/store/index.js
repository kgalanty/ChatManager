import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
Vue.use(Vuex)
const chatsStore = {
  namespaced: true,
  state: () => ({
    chats: [],
    chatsPage: 1,
    chatsLoading: true,
    chatsPerPage: 25,
    filters: { dateFrom: null, dateTo: null },
  }),
  mutations: {
    setChats(state, chats) {
      Vue.set(state, 'chats', chats);
      //state.chats = chats
    },
    setChatsPage(state, page) {
      state.chatsPage = page
    },
    setChatsLoading(state, chatsLoading) {
      state.chatsLoading = chatsLoading
    },
    setFilter(state, filterItem) {
      state.filters = { ...state.filters, ...filterItem }
    }
  },
  actions: {
    loadChats(context) {
      context.commit('setChatsLoading', true)
      const params = [
        `module=ChatManager`,
        `c=ChatTable`,
        `json=1`,
        `page=${context.state.chatsPage}`,
        `perpage=${context.state.chatsPerPage}`,
        `datefrom=${context.state.filters.dateFrom ?? ''}`,
        `dateto=${context.state.filters.dateTo ?? ''}`,
        `tz=`
      ].join("&");

      //context.commit('setChatsPage', payload.chatsPage)
      axios
        .get('addonmodules.php?' + params)
        .then((response) => {
          if (response) {

            context.commit('setChats', response.data)
            context.commit('setChatsLoading', false)

          }
        })
        .catch((e) => {
          console.log(e)
          //window.location = 'login.php'
        });
    }
  },
  getters: {

  }
}
export default new Vuex.Store({
  state: {
    // chats: [],
    // chatsPage: 1,
    // chatsLoading: true,
    // chatsPerPage: 25,
    // filters: { dateFrom: null, dateTo: null },

    groupMember: 0

  },
  mutations: {
    setGroupMember(state, val) {
      state.groupMember = val.results.perm
    },
    // setChats(state, chats) {
    //   Vue.set(state, 'chats', chats);
    //   //state.chats = chats
    // },
    // setChatsPage(state, page) {
    //   state.chatsPage = page
    // },
    // setChatsLoading(state, chatsLoading) {
    //   state.chatsLoading = chatsLoading
    // },
    // setFilter(state, filterItem) {
    //   state.filters = { ...state.filters, ...filterItem }
    // }
  },
  actions: {
    getPermissions(context) {
      return new Promise((resolve,reject) => {
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
    // loadChats(context) {
    //   context.commit('setChatsLoading', true)
    //   const params = [
    //     `module=ChatManager`,
    //     `c=ChatTable`,
    //     `json=1`,
    //     `page=${context.state.chatsPage}`,
    //     `perpage=${context.state.chatsPerPage}`,
    //     `datefrom=${context.state.filters.dateFrom ?? ''}`,
    //     `dateto=${context.state.filters.dateTo ?? ''}`,
    //     `tz=`
    //   ].join("&");

    //   //context.commit('setChatsPage', payload.chatsPage)
    //   axios
    //     .get('addonmodules.php?' + params)
    //     .then((response) => {
    //       if (response) {

    //         context.commit('setChats', response.data)
    //         context.commit('setChatsLoading', false)

    //       }
    //     })
    //     .catch((e) => {
    //       console.log(e)
    //       //window.location = 'login.php'
    //     });
    //}
  },
  modules: {
    chat: chatsStore
  }
})
