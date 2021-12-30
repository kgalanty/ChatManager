import axios from "axios";
import requestMixin from "@/mixins/requestsMixin";
const OperatorsStore =
{
    namespaced: true,
    state: () => ({
        operators: []
    }),
    mutations:
    {
        setOps(state, o) {
            state.operators = o
        },
    },
    actions:
    {
        loadOps(context) {
            const params = requestMixin.methods.generateParamsForRequest("Agents", [
                "a=GetAgentsList",
                "q=",
            ]);
            axios
                .get(`addonmodules.php?${params}`)
                .then(({ data }) => {
                    context.commit('setOps', data.data)
                })
                .catch((error) => {
                    context.commit('setOps', [])
                    throw error;
                })
                .finally(() => {
                });
        }
    }
}
export default OperatorsStore