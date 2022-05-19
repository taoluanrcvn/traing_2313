<template>
  <v-container fluid >
    <v-form>
      <v-row>
        <v-col
            cols="12"
            sm="4"
        >
          <v-text-field
              v-model="search.name"
              label="Tên sản phẩm"
              dense
              outlined
          ></v-text-field>
        </v-col>

        <v-col
            cols="12"
            sm="4"
        >
          <v-select
              :items="search.status"
              label="Tình trạng"
              dense
              outlined
          ></v-select>
        </v-col>
        <v-col
            cols="12"
            sm="2"
        >
          <v-text-field
              v-model="search.min_price"
              label="Giá bán từ"
              dense
              outlined
          ></v-text-field>
        </v-col>
        <v-col
            cols="12"
            sm="1"
        >
          <v-text-field
              v-model="search.max_price"
              label="Giá bán đến"
              dense
              outlined
          ></v-text-field>
        </v-col>
      </v-row>
    </v-form>

  </v-container>
</template>

<script>
export default {
  data() {
    return {
      loggedIn: false,
      namePage : 'Products',
      search: {
        name: null,
        status: ['Đã bán', 'Ngừng bán'],
        min_price: 0,
        max_price: 0,
      }

    }
  },
  created() {
    console.log(localStorage.getItem('token'))
  }
}
</script>

<style scoped>

</style>
