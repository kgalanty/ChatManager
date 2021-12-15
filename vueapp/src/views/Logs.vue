<template>
  <div class="panel-block loading-container">
    <article
      class="panel column is-full"
      style="width: 100% !important"
    ><p
        class="panel-heading"
      >
       Filters
      </p>
    <LogFilters />
       <p
        class="panel-heading"
      >
       Recent System Logs
      </p>
      <SystemLogs></SystemLogs>
    </article>
  </div>
</template>
<style scoped>

</style>
<script>
 import SystemLogs from '@/components/SystemLogs.vue'
 import LogFilters from '@/components/LogFilters.vue'
 import { mapActions } from "vuex";
export default {
  
  name: "Logs",
  components: {
    SystemLogs,
    LogFilters 
  },
  methods: {
   ...mapActions({
      getPermissions: "getPermissions",
    }),
  },
  mounted() {
    this.getPermissions()
  },
  computed: {
  },
  data() {
    return {
     
    };
  },
};
</script>
