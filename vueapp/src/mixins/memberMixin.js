import { mapState } from "vuex";
export default {
    methods: {
       isAdmin()
       {
           return this.groupMember == 2
       },
       isAgent()
       {
           return this.groupMember == 1
       }
    },
    computed: {
        ...mapState(["groupMember"]),
      },
}
