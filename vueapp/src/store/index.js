import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
Vue.use(Vuex)
const chatsLogStore = 
{
  namespaced: true,
  state: () => ({
    logs: [],
    loading: false
  }),
  mutations:
  {
    setLogs(state,logs)
    {
      state.logs = logs
    },
    setLoading(state, loading)
    {
      state.loading = loading
    }
  },
  actions:
  {
    loadLogs(context, payload)
    {
      context.commit('setLoading', true)
      const params = [
        `module=ChatManager`,
        `c=LogsHistory`,
        `json=1`,
        `itemid=${payload.itemid}`
      ].join("&");

      //context.commit('setChatsPage', payload.chatsPage)
      axios
        .get('addonmodules.php?' + params)
        .then((response) => {
          if (response) {

            context.commit('setLogs', response.data)
           

          }
        })
        .catch((e) => {
          console.log(e)
          //window.location = 'login.php'
        })
        .finally(() => 
        {
          context.commit('setLoading', false)
        })
    }
  }
}

const chatsStore = {
  namespaced: true,
  state: () => ({
    chats: [],
    chatsPage: 1,
    chatsLoading: true,
    pendingChatsLoading: true,
    pendingchats: [],
    chatsPerPage: 25,
    filters: { dateFrom: null, dateTo: null },
  }),
  mutations: {
    setChats(state, chats) {
      Vue.set(state, 'chats', chats);
      //state.chats = chats
    },
    setPendingChats(state, chats) {
      Vue.set(state, 'pendingchats', chats);
      //state.chats = chats
    },
    setChatsPage(state, page) {
      state.chatsPage = page
    },
    setChatsLoading(state, chatsLoading) {
      state.chatsLoading = chatsLoading
    },
    setPendingChatsLoading(state, chatsLoading) {
      state.pendingChatsLoading = chatsLoading
    },
    setFilter(state, filterItem) {
      state.filters = { ...state.filters, ...filterItem }
    }
  },
  actions: {
    loadPendingChats(context) {
      context.commit('setPendingChatsLoading', true)
      const params = [
        `module=ChatManager`,
        `c=ChatTable`,
        `json=1`,
        `pending=1`,
        // `page=${context.state.chatsPage}`,
        // `perpage=${context.state.chatsPerPage}`,
        `datefrom=${context.state.filters.dateFrom ?? ''}`,
        `dateto=${context.state.filters.dateTo ?? ''}`,
        `tz=`
      ].join("&");

      //context.commit('setChatsPage', payload.chatsPage)
      axios
        .get('addonmodules.php?' + params)
        .then((response) => {
          if (response) {

            context.commit('setPendingChats', response.data)
            context.commit('setPendingChatsLoading', false)

          }
        })
        .catch((e) => {
          console.log(e)
          //window.location = 'login.php'
        });
    },
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
    chat: chatsStore,
    chatlogs: chatsLogStore
  }
})
