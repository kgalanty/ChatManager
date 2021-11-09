import axios from 'axios'
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
            })
        }
      }
    }
  }
export default tagsStore  