const StaffOnline =
{
    namespaced: true,
    state: () => ({
        intervalIdentifier: null
    }),
    mutations:
    {
        setidentifier(state, v) {
            state.intervalIdentifier = v
        },
    },
    actions:
    {
    }
}
export default StaffOnline