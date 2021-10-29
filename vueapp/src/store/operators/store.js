import axios from 'axios'
import Vue from 'vue'
const operatorsStore = {
    namespaced: true,
    state: () => ({
     operators: []
    }),
    mutations: {
      setOps(state, ops) {
        Vue.set(state, 'operators', ops);
        //state.chats = chats
      },
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
  export default chatsStore