<template>
  <div v-if="points > 0" class="extrapointscolumn">
    <b-tag type="is-success">{{ points }}</b-tag>
  </div>
</template>
<style>
</style>
<style scoped>
</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import tableHelper from "@/mixins/tableHelper";

export default {
  name: "TablePoints",
  props: ["tags", "invoiceStatus"],
  mixins: [tableHelper],
  components: {},
  methods: {},
  mounted() {
    //this.points = this.calculatePointsFromTags(this.tags, this.invoiceStatus)
  },
  beforeUpdate() {
    // this.points = this.calculatePointsFromTags(this.tags, this.invoiceStatus)
  },
  computed: {
    points()
    {
      return this.calculatePointsFromTags(this.tags, this.invoiceStatus)
    }
    //  ...mapState(["chats", "chatsPage", "chatsLoading"]),
  },
  data() {
    return {
    //  points: 0,
    };
  },
  watch: {},
};
</script>
