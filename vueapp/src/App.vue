<template>
  <div id="app">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@5.8.55/css/materialdesignicons.min.css">
    <!-- <div id="nav" class="hover">
       <router-link to="/">Home</router-link> 
      <router-link to="/about">About</router-link>
    </div> -->
    <router-view/>
  </div>
</template>
<style>
.pagination-list a
{
  background: white;
}
.pagination-link
{
  background: white;
}
</style>
<style scoped>

#app .hover a {
  text-decoration: none;
  position: relative;
  margin:0 10px;
}
#app .hover a::after {
  position: absolute;
  content: "";
  height: .05em;
  top: 100%;
  background: rgb(26,77,128);
  z-index: 100;
  left: 0;
  right: 0;
  transform: scaleX(0);
  transition: transform .3s cubic-bezier(.95, .05, .795, .035);
}

#app .hover a:hover::after {
  transition-timing-function: cubic-bezier(.19, 1, .22, 1);  
  transform: scaleX(1);
}
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#app #nav {
  padding: 30px;
}

#app #nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}
</style>
