import axios from 'axios'
import requestsMixin from '../../mixins/requestsMixin'
const SystemLogs =
{
  namespaced: true,
  state: () => ({
    logs: { data: [], total: 0 },
    loading: false,
    page: 1,
    filters: { dateFrom: null, dateTo: null, operator: null },
    query: ''
  }),
  mutations:
  {
    setQuery(state, query) {
      state.query = query
    },
    setLogs(state, logs) {
      state.logs = logs
    },

    setLoading(state, loading) {
      state.loading = loading
    },
    setPage(state, page) {
      state.page = page
    },
    setFilter(state, filterItem) {
      state.filters = { ...state.filters, ...filterItem }
    },

  },
  actions:
  {
    setOperatorFilter(context, payload) {
      context.commit("setFilter", { operator: payload });
      context.commit("setPage", 1);
    },
    loadLogs(context) {
      context.commit('setLoading', true)
      return new Promise((resolve, reject) => {
        const Params = requestsMixin.methods.generateParamsForRequest('SystemLogs', [`page=${context.state.page}`,
        `datefrom=${context.state.filters.dateFrom ?? ''}`,
        `dateto=${context.state.filters.dateTo ?? ''}`,
        `operator=${context.state.filters.operator ? context.state.filters.operator : ''}`,
        `q=${context.state.query}`
        ])
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