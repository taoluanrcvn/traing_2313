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

      <v-chip
          class="ma-2"
          color="indigo"
          text-color="white"
      >
        <v-avatar left>
          <v-icon>mdi-account-circle</v-icon>
        </v-avatar>
        {{user.name}}
      </v-chip>

      <v-chip
          class="ma-2"
          color="orange"
          text-color="white"
      >
        {{user.group_role}}
        <v-icon right>
          mdi-star
        </v-icon>
      </v-chip>
      <template v-if="$vuetify.breakpoint.smAndUp">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                v-bind="attrs"
                v-on="on"
                icon
                @click="logout()"
            >
              <v-icon color="red">mdi-exit-to-app</v-icon>
            </v-btn>
          </template>
          <span>Thoát</span>
        </v-tooltip>

      </template>
    </v-toolbar>
</template>

<script>
import {ServiceUser} from "@/service/service.user";

export default {
  name: "Header",
  props: ['userCurrent'],
  data() {
    return {
      activeTab: '',
      items: [
        { title: 'Sản phẩm', path: '/products', active: true },
        { title: 'Khách hàng', path: '/customers', active: false },
        { title: 'Người dùng', path: '/users' , active: false},
      ],
      user: JSON.parse(localStorage.getItem('user')),
    }
  },
  created() {
  },
  methods: {
    logout () {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      this.$router.push('login')
    },

  }
}
</script>

<style scoped>

</style>
