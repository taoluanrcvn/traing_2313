<template src="./ProductTemplate.html"></template>
<script>

import Header from "@/components/Header";
import DialogEditAndAddProduct from "@/components/DialogEditAndAddProduct";
import i18n from "@/plugins/i18n";
import {ServiceCustomer} from "@/service/service.customer";
import {ServiceProduct} from "@/service/serve.product";
import {Toast} from "@/utils/toast";
import Swal from "sweetalert2";
import {Product} from "@/utils/class.user";
export default {
  components: {Header, DialogEditAndAddProduct},
  data() {
    return {
      loggedIn: false,
      statusProducts: [
          { value: 0 , text : i18n.t('product.status.not_sale')},
          { value: 1 , text : i18n.t('product.status.sale')},
          { value: 2 , text : i18n.t('product.status.sale_out')}
      ],
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
        { text: i18n.t('product.label.name'), value: 'product_name', class: "white--text", sortable: false},
        { text: i18n.t('product.label.deception'), value: 'description', class: "white--text", sortable: false},
        { text: i18n.t('product.label.price'), value: 'product_price', class: "white--text", sortable: false},
        { text: i18n.t('product.label.status'), value: 'is_sales', class: "white--text", sortable: false},
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
      minPriceRules: [
        v => v >= 0 || i18n.t('roles.price_min'),
      ],
      maxPriceRules: [
        v => v >= 0 || i18n.t('roles.price_min'),
      ],
      validFormSearch: false,
      dialogAddProduct: false,
      dialogEditProduct: false,
      productSelected: new Product()
    }
  },
  watch: {
    async page() {
      await this.getListProducts()
    },
    itemsPerPage() {
      this.getListProducts()
    }
  },
  created() {
    this.getListProducts();
  },
  methods: {
    async getListProducts() {
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
          if (response && response.statusCode) {
            const products = response.data;
            this.pageLength = products.last_page;
            this.products = products.data;
            this.totalProduct = products.total;
            this.productFrom = products.from;
            this.productTo = products.to;
          }
        } catch (e) {

        }
        finally {
          this.loadingTable = false
        }
    },
    async clearSearch() {
      this.page = 1
      this.search.name = '';
      this.search.isSale = '';
      this.search.minPrice = 0;
      this.search.maxPrice = 0;
      this.isSearch = 0;
      await this.getListProducts();
    },
    async searchProduct() {
      if (!this.hasSearch()) {
        await Toast.show('warning', i18n.t('notification.not_enter_search'));
      }
      const isValid = this.$refs.form.validate()
      if (this.hasSearch() && isValid) {
        this.isSearch = true;
        this.page = 1
        await this.getListProducts();
      }
    },

    showDialogEditProduct(product) {
      if (!this.hasPermissionEditProduct()) {
        Toast.show('warning', i18n.t('permission.not'));
        return;
      }
      this.productSelected = product;
      this.dialogEditProduct = true;

    },
    showDialogAddProduct(product) {
      if (!this.hasPermissionAddProduct()) {
        Toast.show('warning', i18n.t('permission.not'));
        return;
      }
      this.productSelected = product;
      this.dialogAddProduct = true;
    },

    hasPermissionAddProduct() {
      if (!this.userCurrent.is_admin) {
        return false
      }
      return true;
    },

    hasPermissionEditProduct() {
      if (this.userCurrent.group_role === i18n.t('group_role.reviewer')) {
        return false
      }
      return true;
    },

    showConfirmDeleteProduct(item) {
      if (!this.userCurrent.is_admin) {
        Toast.show('warning', i18n.t('permission.not'));
        return;
      }
      Swal.fire({
        title: `Bạn có muốn xóa sản phẩm ${item.product_name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
      }).then(async (result) => {
        if (result.isConfirmed) {
          await this.deleteProduct(item)
        }
      })
    },

    async deleteProduct(product) {
      try {
        const response = await ServiceProduct.deleteUser(product)
        if (response.statusCode) {
          await this.getListProducts()
          if (this.products.length === 0 && this.page > 1) {
            this.page--
          }
          await Toast.show('success', i18n.t('notification.delete_success'));
        }
      } catch (e) {
        if (e.status && e.status === 422) {
          const errors = e.data.messages;
          await Toast.show('error', errors.detail)
        }
        await Toast.show('success', i18n.t('notification.delete_fail'));
      }
    },

    hasSearch() {
      if (!this.search.maxPrice && !this.search.minPrice && !this.search.name && this.search.isSale === null ) {
        return false;
      }
      return true;
    },

    funcSuccess(productNew) {
      this.getListProducts()
    },
    nameKeydown(e) {
      if (!/^\d*$/.test(e.key) && e.key  !== 'Backspace') {
        e.preventDefault();
      }
    },
  }
}
</script>

<style scoped>

</style>
