import axios from 'axios'
import requestMixin from "@/mixins/requestsMixin";
const tagsStore = {
    namespaced: true,
    state: () => ({
      tags: []
    }),
    mutations: {
      setTags(state, tags) {
        state.tags = tags
      }
    },
    actions:
    {
      loadTags(context) {
        if (context.state.tags.length == 0) {
          const params = requestMixin.methods.generateParamsForRequest("Tags",[`a=GetDistinctTags`] )      
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