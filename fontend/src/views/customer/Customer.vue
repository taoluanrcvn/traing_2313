<template src="./CustomerTemplate.html"></template>
<script>
import Header from "@/components/Header";
import {ServiceCustomer} from "@/service/service.customer";
import {Toast} from "@/utils/toast";

export default {
  components: {Header},
  name: "Customer",
  data() {
    return {
      search: {
        name: '',
        email: '',
        isActive: '',
        address: '',
      },
      pageLength: 0,
      pageCount: 0,
      itemsPerPage: 10,
      page: 1,
      listStatus: [{ value: 0 , text : 'Tạm dừng'}, { value: 1 , text : 'Đang hoạt động'}],
      perPages: [10, 15, 20],
      customers: [],
      headers: [
        {
          text: '#',
          align: 'start',
          sortable: false,
          value: 'id',
          class: "white--text"
        },
        { text: 'Họ tên', value: 'customer_name', class: "white--text", sortable: false},
        { text: 'Email', value: 'email', class: "white--text", sortable: false},
        { text: 'Địa chỉ', value: 'address', class: "white--text", sortable: false},
        { text: 'Số điện thoại', value: 'tel_num', class: "white--text", sortable: false},
        { text: 'Hành động', value: 'act', class: "white--text", sortable: false},
      ],
      loadingTable: false,
      totalCustomer: 0,
      customerFrom: 0,
      customerTo: 0
    }
  },
  watch: {
    page() {
      this.getCustomers()
    },
    itemsPerPage() {
      this.getCustomers()
    },
    'search.name' (value) {
      if (value.includes(' ')) {
        this.search.name = value.replace(/^\s+|\s+$/gm,'')
      }
    },
    'search.email' (value) {
      if (value.includes(' ')) {
        this.search.email = value.replace(/^\s+|\s+$/gm,'')
      }
    },
    'search.address' (value) {
      if (value.includes(' ')) {
        this.search.address = value.replace(/^\s+|\s+$/gm,'')
      }
    }
  },
  created() {
    this.getCustomers();
  },
  methods: {
    async getCustomers() {
      try {
        this.loadingTable = true;
        const params =  {
          page: this.page,
          perPage: this.itemsPerPage,
          customer_name: this.search.name,
          email: this.search.email,
          address: this.search.address,
          isActive: this.search.isActive,
          isSearch: this.isSearch,
        }
        const response  = await ServiceCustomer.getCustomers(params);
        if (response && response.statusCode) {
          const customers = response.data;
          this.pageLength = customers.last_page;
          this.customers = customers.data;
          this.totalCustomer = customers.total;
          this.customerFrom = customers.from;
          this.customerTo = customers.to;
        }
      } catch (e) {
        console.log(e)
      }
      finally {
        this.loadingTable = false;
      }
    },

    async searchCustomer() {
      if (this.hasSearch()) {
        this.isSearch = true;
        this.page = 1
        await this.getCustomers();
      } else {
        await Toast.show('warning', 'Vui lòng nhập vào thông tin để tìm kiếm!');
      }
    },
    async clearSearch() {
      if (this.hasSearch()) {
        this.search.address = '';
        this.search.isActive = '';
        this.search.customer_name = '';
        this.search.email = '';
        this.isSearch = 0;
        await this.getCustomers();
      }
    },
    showDialogAddCustomer() {

    },
    showDialogEditCustomer() {

    },
    hasSearch() {
      if (this.search.name || this.search.email || this.search.address || (this.search.isActive === 1 || this.search.isActive === 0)) {
        return true
      }
      return false
    },
  }
}
</script>

<style scoped>

</style>
