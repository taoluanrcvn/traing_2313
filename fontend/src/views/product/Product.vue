<template src="./ProductTemplate.html"></template>
<script>

import Header from "@/components/Header";
import i18n from "@/plugins/i18n";
import {ServiceCustomer} from "@/service/service.customer";
import {ServiceProduct} from "@/service/serve.product";
export default {
  components: {Header},
  data() {
    return {
      loggedIn: false,
      statusProducts: [{ value: 0 , text : i18n.t('product.status.sale_off')}, { value: 1 , text : i18n.t('product.status.sale')}],
      search: {
        name: null,
        isSale: null,
        minPrice: 0,
        maxPrice: 0,
      },
      headers: [
        {
          text: '#',
          align: 'start',
          sortable: false,
          value: 'id',
          class: "white--text"
        },
        { text: i18n.t('product.label.name'), value: 'customer_name', class: "white--text", sortable: false},
        { text: i18n.t('product.label.deception'), value: 'email', class: "white--text", sortable: false},
        { text: i18n.t('product.label.price'), value: 'address', class: "white--text", sortable: false},
        { text: i18n.t('product.label.status'), value: 'tel_num', class: "white--text", sortable: false},
        { text: i18n.t('product.label.action'), value: 'act', class: "white--text", sortable: false},
      ],
      products: [],
      perPages: [10, 15, 20],
      itemsPerPage: 10,
      pageLength: 0,
      page: 1,
      totalProduct: 0,
      productFrom: 0,
      productTo: 0,
      pageCount: 0,
      loadingTable: false,
      userCurrent: JSON.parse(localStorage.getItem('user')),
    }
  },
  created() {
    this.getListUser();
  },
  methods: {
    async getListUser() {
        try {
          this.loadingTable = true;
          const params =  {
            page: this.page,
            perPage: this.itemsPerPage,
            name: this.search.name,
            isSale: this.search.isSale,
            minPrice: this.search.minPrice,
            maxPrice: this.search.maxPrice,
            isSearch: this.isSearch,
          }
          const response  = await ServiceProduct.getProducts(params);
          console.log(response)
        } catch (e) {

        }
        finally {
          this.loadingTable = false
        }
    },
    clearSearch() {

    },
    searchUser() {},

    showDialogEditProduct() {

    },
    showDialogAddUser() {

    },
    showDialogDeleteCustomer() {

    }
  }
}
</script>

<style scoped>

</style>
