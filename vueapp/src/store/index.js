import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import Buefy from 'buefy'
Vue.use(Vuex)
Vue.use(Buefy)
const chatsLogStore =
{
  namespaced: true,
  state: () => ({
    logs: [],
    loading: false
  }),
  mutations:
  {
    setLogs(state, logs) {
      state.logs = logs
    },
    setLoading(state, loading) {
      state.loading = loading
    }
  },
  actions:
  {
    loadLogs(context, payload) {
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
        .finally(() => {
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
    filters: { dateFrom: null, dateTo: null, operator: null },
    query: ''
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
    },
    setQuery(state, query)
    {
      state.query = query
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
        `tz=`,
       
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
      return new Promise((resolve, reject) => {
        context.commit('setChatsLoading', true)
        const params = [
          `module=ChatManager`,
          `c=ChatTable`,
          `json=1`,
          `page=${context.state.chatsPage}`,
          `perpage=${context.state.chatsPerPage}`,
          `datefrom=${context.state.filters.dateFrom ?? ''}`,
          `dateto=${context.state.filters.dateTo ?? ''}`,
          `tags=${context.state.filters.tags ? context.state.filters.tags : ''}`,
          `operator=${context.state.filters.operator ? context.state.filters.operator : ''}`,
          `tz=`,
          `q=${context.state.query}`
        ].join("&");

        //context.commit('setChatsPage', payload.chatsPage)
        axios
          .get('addonmodules.php?' + params)
          .then((response) => {
            if (response) {
              for(let item of response.data.data)
              {
                item.tags.sort(function(a, b){
                  if(a.tag === 'sales') return -1;
                  if(b.tag === 'sales') return 1;
                  if(a.tag == 'directsale' || a.tag == 'wcb' || a.tag == 'cannotoffer') return -1;
                  return 1;
                })
              }
              //Sorting by tags
              // Sales should be first, directsale/wcb/cannotoffer are second and then everything else. 
              context.commit('setChats', response.data)
              context.commit('setChatsLoading', false)
              resolve()

            }
          })
          .catch(() => {
            context.commit('setChats', [])
            context.commit('setChatsLoading', false)
            reject('Failed to download data. Check if you are logged in and have permission.')
            return
            //console.log(e)
            //window.location = 'login.php'
          });

      })
    }
  },
  getters: {

  }
}
const tagsStore = {
  namespaced: true,
  state: () => ({
    tags: []


  }),
  mutations: {
    setTags(state, tags) {
      state.tags = tags
      //  // Vue.set(state, 'chats', chats);
      //state.chats = chats
    }
  },
  actions:
  {
    loadTags(context) {
      if (context.state.tags.length == 0) {
        const params = [
          `module=ChatManager`,
          `c=Tags`,
          `json=1`,
          `a=GetDistinctTags`].join("&");
        axios
          .get('addonmodules.php?' + params)
          .then((response) => {

            if (response.data.result == 'success') {
              context.commit('setTags', response.data.data)
              return
            }
            console.log(response.data)
          })
      }
    }
  }
}
const chatcolumnsStore = {
  namespaced: true,
  state: () => ({
    filters: {
      date: { title: "Date", display: true },
      operator: { title: "Operator", display: true },
      chatid: { title: "Chat ID", display: true },
      tags: { title: "Tags", display: true },
      allchats: { title: "All Chats", display: true },
      name: { title: "Client's Name", display: true },
      email: { title: "Client's E-mail", display: true },
      domain: { title: "Domain", display: true },
      location: { title: "Location", display: true },
      ip: { title: "IP", display: true },
      followup: { title: "Follow Up", display: true },
      orderid: { title: "Order ID", display: true },
      extrapoints: { title: "Extra Points", display: true },
      edit: { title: "Edit", display: true },
    },
  }),
  getters:
  {
    filters: state => {
      return state.filters
    }
  },
  mutations: {
    setFilters(state, filters) {
      state.filters = filters
      //  // Vue.set(state, 'chats', chats);
      //state.chats = chats
    }
  },
  actions:
  {

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

  },
  actions: {
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
    tags: tagsStore
  }
})
