<template>
    <v-toolbar dense height="80" elevation="1">
      <v-toolbar-title>
        <img src="../assets/logo_rivercrane.png" height="50" width="120"/>
      </v-toolbar-title>
      <v-toolbar-items>
        <v-btn v-for="item in items" text :to="{ path: item.path }">
          {{item.title}}
        </v-btn>
      </v-toolbar-items>

      <v-spacer></v-spacer>

      <template v-if="$vuetify.breakpoint.smAndUp">
        <v-btn icon :to="{ path: '/login' }">
          <v-icon>mdi-export-variant</v-icon>
        </v-btn>
      </template>
    </v-toolbar>
</template>

<script>
export default {
  name: "Header",
  data() {
    return {
      activeTab: '',
      items: [
        { title: 'Sản phẩm', path: '/products', active: true },
        { title: 'Khách hàng', path: '/customers', active: false },
        { title: 'Người dùng', path: '/users' , active: false},
      ],
    }
  },
  computed:{
  }
}
</script>

<style scoped>

</style>
