<template>
  <div class="columns is-desktop">
    <div class="column" v-for="(column, index) in filters" :key="index">
      <b-checkbox v-model="column.display">
        {{ column.title }}
      </b-checkbox>
    </div>
  </div>
</template>
<style>
.dropdown-content > a {
  text-align: left !important;
}
</style>
<style scoped>
.btable {
  font-size: 13px;
}
article > .panel-heading {
  background: rgb(165, 197, 255);
  background: linear-gradient(
    180deg,
    rgba(165, 197, 255, 1) 0%,
    rgba(40, 127, 207, 1) 100%
  );
}
.tile {
  margin-bottom: 10px;
}
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";

export default {
  name: "ColumnsFilter",
  components: {
    //HelloWorld
  },
  methods: {
    ...mapActions({
      //loadChats: "filters/loadColumnFilters",
    }),
  },
  mounted() {
    if(localStorage.getItem('chatcolumnsfilters'))
    {
      this.filters = JSON.parse(localStorage.getItem('chatcolumnsfilters'))
    }
    else
    {
      this.filters = this.$store.state.chatcolumns.filters;
    }
  },
  computed: {
    ...mapState([]),
    // filters:
    // {
    //   get()
    //   {
    //     return this.$store.state.chatcolumns.filters
    //   },
    //   set(val)
    //   {
    //    console.log(val)
    //   }
    // }
  },
  data() {
    return {
      filters: {},
    };
  },

  watch: {
    filters: {
      deep: true,
      handler(val) {
        this.$store.commit("chatcolumns/setFilters", val);
        localStorage.setItem('chatcolumnsfilters', JSON.stringify(this.$store.state.chatcolumns.filters));
      },
    },
  },
};
</script>
