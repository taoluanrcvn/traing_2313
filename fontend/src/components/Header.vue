<template>
  <v-toolbar dense height="80" elevation="1">
<!--    <v-app-bar-nav-icon></v-app-bar-nav-icon>-->

    <v-toolbar-title>
      <img src="../assets/logo_rivercrane.png" alt="" width="150" height="50">
    </v-toolbar-title>

    <v-spacer></v-spacer>
    <v-toolbar-items>

    </v-toolbar-items>
  </v-toolbar>
</template>

<script>
export default {
  name: "Header",
  data() {
    return {
      dialog: false,
      nav: [
        {
          icon: 'home',
          text: 'Home',
          title: 'Back to Home page',
          active: true
        },
        {
          icon: 'info',
          text: 'About',
          title: 'About this demo',
          active: false
        },
        {
          icon: 'assignment_turned_in',
          text: 'Todos',
          title: 'Some stuff that needs doing',
          active: false
        },
        {
          icon: 'email',
          text: 'Contact',
          title: 'Our Contact info',
          active: false
        }
      ]
    }
  }
}
</script>

<style scoped>

</style>