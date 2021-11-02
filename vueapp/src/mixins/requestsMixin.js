
export default {
    methods: {
        generateParamsForRequest(controller) {
            return [`module=ChatManager`, `c=${controller}`, `json=1`].join("&");
        },
        
    }
}
