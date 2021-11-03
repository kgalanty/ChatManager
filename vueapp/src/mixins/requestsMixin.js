
export default {
    methods: {
        generateParamsForRequest(controller, params = []) {
            return [`module=ChatManager`, `c=${controller}`, `json=1`].concat(params).join("&");
        },
        
    }
}
