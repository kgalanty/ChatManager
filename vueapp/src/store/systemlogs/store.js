import axios from 'axios'
import requestsMixin from '../../mixins/requestsMixin'
const SystemLogs =
{
  namespaced: true,
  state: () => ({
    logs: { data: [], total: 0 },
    loading: false,
    page:1
  }),
  mutations:
  {
    setLogs(state, logs) {
      state.logs = logs
    },

    setLoading(state, loading) {
      state.loading = loading
    },
    setPage(state, page)
    {
      state.page = page
    }
  },
  actions:
  {

    loadLogs(context) {
      context.commit('setLoading', true)
      return new Promise((resolve, reject) => {
        const Params = requestsMixin.methods.generateParamsForRequest('SystemLogs', [`page=${context.state.page}`])
        axios
          .get('addonmodules.php?' + Params)
          .then(response => {
            if (response.data.data) {
              context.commit('setLogs', response.data)
              resolve()
            }
            if (response.data.error) {
              reject(response.data.error)
            }
          })
          .catch((e) => {
            reject(e)
            //window.location = 'login.php'
          })
          .finally(() => {
            context.commit('setLoading', false)
          })
      })



    }
  }
}
export default SystemLogs