import axios from 'axios'
import Vue from 'vue'
import requestsMixin from '../../mixins/requestsMixin'
const chatsStore = {
  namespaced: true,
  state: () => ({
    chats: [],
    chatsPage: 1,
    chatsLoading: true,
    pendingChatsLoading: true,
    pendingchats: [],
    chatsPerPage: 25,
    filters: { dateFrom: null, dateTo: null, operator: null, tags: '', extags: '' },
    query: '',
    intervalid: null
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
    setQuery(state, query) {
      state.query = query
    },
    setIntervalID(state, intervalid)
    {
      state.intervalid = intervalid
    }
  },
  actions: {
    setTagsFilter(context, payload) {
      context.commit("setFilter", { tags: payload });
      context.commit("setChatsPage", 1);
    },
    setQueryFilter(context, payload)
    {
      context.commit('setQuery', payload)
      context.commit("setChatsPage", 1);
    },
    setExTagsFilter(context, payload) {
      context.commit("setFilter", { extags: payload });
      context.commit("setChatsPage", 1);
    },
    setDateFromFilter(context, payload) {
      context.commit("setFilter", { dateFrom: payload });
      context.commit("setChatsPage", 1);
    },
    setDateToFilter(context, payload) {
      context.commit("setFilter", { dateTo: payload });
      context.commit("setChatsPage", 1);
    },
    setOperatorFilter(context, payload) {
      context.commit("setFilter", { operator: payload });
      context.commit("setChatsPage", 1);
    },
    loadPendingChats(context) {
        context.commit('setPendingChatsLoading', true)
        const params =  requestsMixin.methods.generateParamsForRequest('ChatTable', [`pending=1`])

      axios
        .get('addonmodules.php?' + params)
        .then((response) => {
          if (response) {
            // if (context.state.intervalid) {
            //   clearInterval(context.state.intervalid)
            //   context.commit('setIntervalID', null)
            // }
            // let intervalid = setInterval(() => {
            //   context.dispatch('loadPendingChats')
            // }, 5000)
           // context.commit('setIntervalID', intervalid)
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
        context.commit('setChatsLoading', true) //run loading
        //set total chats to previous value to make it visible during loading
        
        let replaceChars = encodeURIComponent('#')
        let tags = context.state.filters.tags ? context.state.filters.tags.join(',') : ''
        let extags = context.state.filters.extags ? context.state.filters.extags.join(',') : ''
        const params =  requestsMixin.methods.generateParamsForRequest('ChatTable', [ `page=${context.state.chatsPage}`,
        `perpage=${context.state.chatsPerPage}`,
        `datefrom=${context.state.filters.dateFrom ?? ''}`,
        `dateto=${context.state.filters.dateTo ?? ''}`,
        `tags=${tags.replace('#', replaceChars)}`,
        `extags=${extags.replace('#', replaceChars)}`,
        `operator=${context.state.filters.operator ? context.state.filters.operator : ''}`,
        `tz=`,
        `q=${context.state.query}`])

        axios
          .get('addonmodules.php?' + params)
          .then((response) => {
            context.commit('setChats', {'total': 0})
            if (response) {
              for (let item of response.data.data) {
                item.tags.sort(function (a, b) {
                  if (a.tag === 'sales') return -1;
                  if (b.tag === 'sales') return 1;
                  if (a.tag == 'directsale' || a.tag == 'wcb' || a.tag == 'cannotoffer') return -1;
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
export default chatsStore