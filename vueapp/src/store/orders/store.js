import axios from 'axios'
import requestsMixin from '../../mixins/requestsMixin'
const OrdersStore =
{
  namespaced: true,
  state: () => ({
    orders: { data: [], total: 0 },
    loading: false
  }),
  mutations:
  {
    setOrders(state, orders) {
      state.orders = orders
    },

    setLoading(state, loading) {
      state.loading = loading
    }
  },
  actions:
  {

    loadOrders(context) {
      context.commit('setLoading', true)
      return new Promise((resolve, reject) => {
        const Params = requestsMixin.methods.generateParamsForRequest('OrdersChats')
        // context.commit('setLoading', true)
        // const params = [
        //   `module=ChatManager`,
        //   `c=OrdersChats`,
        //   `json=1`,
        // ].join("&");

        //context.commit('setChatsPage', payload.chatsPage)
        axios
          .get('addonmodules.php?' + Params)
          .then(response => {
            if (response.data.data) {
              context.commit('setOrders', response.data)
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
export default OrdersStore