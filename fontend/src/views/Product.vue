<template>
  <h5>{{namePage}}</h5>
</template>

<script>
export default {
  data() {
    return {
      loggedIn: false,
      namePage : 'Products',

    }
  },
  created() {
    console.log(localStorage.getItem('token'))
  }
}
</script>

<style scoped>

</style>
