const ReasonsStore = {
    namespaced: true,
    state: () => ({
        reasons: [
            "I have difficulties using the service",
            "I no longer need the service",
            "I have billing issues",
            "Other",
        ],

        reasonsExtended: [
            {
                options: [
                    "Can’t run xyz",
                    "Resource abuse",
                    "Downtime",
                    "Mail issues",
                    "Technical issues not caused by us",
                ],
            },
            {
                options: ["End of project", "Never used"],
            },
            {
                options: [
                    "High renewal",
                    "COVID",
                    "their customer delays/doesn’t pay",
                    "Better deal elsewhere",
                ],
            },
            {
                options: [
                    "Other abuse (DMCA for example)",
                    "purchased wrong service / change of package",
                    "Unhappy with communication with our teams (response times, unprofessional attitude etc",
                ],
            },
        ]
    })
}
const store = new Vuex.Store({
    modules: {
        reasons: ReasonsStore,
        // groups: GroupsStore
    },
    state: {
        // servers: [],
        // pools: [],
        cancelTokens: [],
        supervisors: [],
        customoffers: [],
        pgroups: [],
        results:
        {
            vip:
            {
                total: 0, data: []
            },
            vps:
            {
                total: 0, data: []
            },
            shared:
            {
                total: 0, data: []
            },
            extra:
            {
                total: 0, data: []
            },

        },
        vipresults: { total: 0, data: [] },
        loadings: {},
        pages: {
            vip:
            {
                page: 1,
                perPage: 10, sorting: {field: "date", order: 'desc'}
            },
            shared:
            {
                page: 1,
                perPage: 10, sorting: {field: "date", order: 'desc'}
            },
            vps:
            {
                page: 1,
                perPage: 10, sorting: {field: "date", order: 'desc'}
            },
            extra:
            {
                page: 1,
                perPage: 10, sorting: {field: "date", order: 'desc'}
            }
        },
        StartFromDate: new Date((new Date()).getFullYear(), (new Date()).getMonth(), 1),
        EndToDate: new Date((new Date()).getFullYear(), (new Date()).getMonth() + 1, 0),
        agents: []
    },
    actions: {
        refreshProductGroups(context) {
            axios
                .get('addonmodules.php?module=ChatManager&c=ProductGroups&json=1')
                .then(response => {
                    //this.api = response.data;
                    context.commit('setProductGroups', response.data.data);
                })
        },
        refreshSupervisors(context) {
            axios
                .get('addonmodules.php?module=ChatManager&c=Supervisors&json=1')
                .then(response => {
                    //this.api = response.data;
                    context.commit('setSupervisors', response.data.data);
                })
        },
        refreshCustomOffers(context) {
            axios
                .get('addonmodules.php?module=ChatManager&c=CustomOffers&json=1')
                .then(response => {
                    //this.api = response.data;
                    context.commit('setOffers', response.data.data);
                })
        },
        CANCEL_PENDING_REQUESTS(context) {

            // Cancel all request where a token exists
            context.state.cancelTokens.forEach((request, i) => {
                if (request.cancel) {
                    request.cancel();
                }
            });

            // Reset the cancelTokens store
            context.commit('CLEAR_CANCEL_TOKENS');
        },
        getAgents(context)
        {
            const params = [
                `module=ChatManager`,
                `c=Agents`,
                `json=1`,
              ].join("&");
              axios.get(`addonmodules.php?${params}`)
                .then((response) => {
                  //console.log(response)
                  if (response.data) {
                    context.commit('setAgents', response.data.data);
                  }
                  //this.isLoading = false
                });
        },
        refreshVip(context) {
            context.commit('setLoadings', { vip: true })
            const params = [
                `module=ChatManager`,
                `c=Request`,
                `json=1`,
                `page=${context.state.pages.vip.page}`,
                `perpage=${context.state.pages.vip.perPage}`,
                `sort=${context.state.pages.vip.sorting.field}`,
                `order=${context.state.pages.vip.sorting.order}`,
                `type=vip`
            ].join("&");
            return axios.get(`addonmodules.php?${params}`)
                .then((response) => {
                    if (response.data) {
                        context.commit('setVip', response.data);
                        context.commit('setLoadings', { vip: false })
                        // this.total = response.data.total
                    }
                });
        },
        refreshVPS(context) {
            context.commit('setLoadings', { vps: true })
            const params = [
                `module=ChatManager`,
                `c=Request`,
                `json=1`,
                `page=${context.state.pages.vps.page}`,
                `perpage=${context.state.pages.vps.perPage}`,
                `sort=${context.state.pages.vps.sorting.field}`,
                `order=${context.state.pages.vps.sorting.order}`,
                `type=vps`
            ].join("&");
            return axios.get(`addonmodules.php?${params}`)
                .then((response) => {
                    if (response.data) {
                        context.commit('setVPS', response.data);
                        context.commit('setLoadings', { vps: false })
                        // this.total = response.data.total
                    }
                });
        },
        refreshShared(context) {
            context.commit('setLoadings', { shared: true })
            const params = [
                `module=ChatManager`,
                `c=Request`,
                `json=1`,
                `page=${context.state.pages.shared.page}`,
                `perpage=${context.state.pages.shared.perPage}`,
                `sort=${context.state.pages.shared.sorting.field}`,
                `order=${context.state.pages.shared.sorting.order}`,
                `type=shared`
            ].join("&");
            return axios.get(`addonmodules.php?${params}`)
                .then((response) => {
                    if (response.data) {
                        context.commit('setShared', response.data);
                        context.commit('setLoadings', { shared: false })
                        // this.total = response.data.total
                    }
                });
        },
        refreshExtra(context) {
            context.commit('setLoadings', { extra: true })
            const params = [
                `module=ChatManager`,
                `c=Request`,
                `json=1`,
                `page=${context.state.pages.extra.page}`,
                `perpage=${context.state.pages.extra.perPage}`,
                `sort=${context.state.pages.extra.sorting.field}`,
                `order=${context.state.pages.extra.sorting.order}`,
                `type=extra`
            ].join("&");
            return axios.get(`addonmodules.php?${params}`)
                .then((response) => {
                    if (response.data) {
                        context.commit('setExtra', response.data);
                        context.commit('setLoadings', { extra: false })
                        // this.total = response.data.total
                    }
                });
        },
        checkPerms()
        {
            return new Promise((resolve, reject) => { axios
            .get('addonmodules.php?module=ChatManager&c=Auth&json=1')
            .then(response => {
                //this.api = response.data;
                if(response.data.results.stats === 1)
                {
                  //localStorage.setItem('LoggedUser', 1)
                  resolve(1);
                }
                else
                {
                  //localStorage.setItem('LoggedUser', 0)
                  resolve(0);
                }
            })
            })
        }

    },
    getters: {
        cancelTokens(state) {
            return state.cancelTokens;
        }
    },
    mutations: {
        setAgents(state, agents)
        {
            state.agents = agents
        },
        setSupervisors(state, supervisors) {
            state.supervisors = supervisors
        },
        setProductGroups(state, pgroups) {
            state.pgroups = pgroups
        },
        setOffers(state, supervisors) {
            state.customoffers = supervisors
        },
        ADD_CANCEL_TOKEN(state, token) {
            state.cancelTokens.push(token);
        },
        CLEAR_CANCEL_TOKENS(state) {
            state.cancelTokens = [];
        },
        setVip(state, results) {
            state.results.vip = results
        },
        setVPS(state, results) {
            state.results.vps = results
        },
        setShared(state, results) {
            state.results.shared = results
        },
        setExtra(state, results) {
            state.results.extra = results
        },
        setLoadings(state, loading) {
            state.loadings = { ...state.loadings, ...loading }
        },
        setPages(state, pages) {
            state.pages = { ...state.pages, ...pages }
        },
        setSorting(state, sorting) {
            console.log(sorting)
            state.pages = { ...state.pages, ...sorting }
        },
        setStartFromDate(state, date)
        {
            state.StartFromDate = date
        },
        setEndToDate(state, date)
        {
            state.EndToDate = date
        }

        
        // setServers(state, servers) {
        //     state.servers = servers;
        // },
        // setPools(state, pools) {
        //     state.pools = pools
        // },
        // setPoolAsPrimary(state, pool_id) {
        //     for (let pool of state.pools) {
        //         if (pool.id === pool_id) {
        //             pool.primary = 1
        //         }
        //         else {
        //             pool.primary = 0
        //         }
        //     }
        // },
        // setPoolLogic(state, pooldata) {
        //     for (let pool of state.pools) {
        //         if (pool.id === pooldata.id) {
        //             pool.logic = pooldata.logic
        //         }

        //     }
        // }
    }
});