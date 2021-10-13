import axios from 'axios'
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
    clearLogs(state)
    {
      state.logs = []
    },
    setLoading(state, loading) {
      state.loading = loading
    }
  },
  actions:
  {
    clearLogs(context)
    {
      context.commit('clearLogs')
    },
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
export default chatsLogStore