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
export default chatcolumnsStore