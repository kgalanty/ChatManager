<template>
 <span>{{current}}
</template>
<style >

</style>
<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from "vuex";

export default {
  name: "FollowUpTimer",
  components: {},
  props: ['initial'],
  methods: {
   onInterval() {
      this.current = this.current -= this.interval
      if (this.current <= 0) {
        clearInterval(this.timerInterval)
        this.current = 0
      }
    }
  },
  mounted() {
    setInterval(this.onInterval, this.interval * 1000)
  },
  computed: {
   
  },
  data() {
    return {
      current: this.initial,
      timerInterval: null
    };
  },
};
</script>
