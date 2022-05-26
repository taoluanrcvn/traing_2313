<template src="./CustomerTemplate.html"></template>
<script>
import Header from "@/components/Header";
import DialogEditAndAddCustomer from "@/components/DialogEditAndAddCustomer";
import {ServiceCustomer} from "@/service/service.customer";
import {Toast} from "@/utils/toast";
import Papa from 'papaparse';
import {Customer} from "@/utils/class.user";
import moment from 'moment';
import i18n from "@/plugins/i18n";
import Login from "@/views/Login";
import Swal from "sweetalert2";

export default {
  components: {Header, DialogEditAndAddCustomer},
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
      listStatus: [{ value: 0 , text : 'Tạm dừng' }, { value: 1 , text : 'Đang hoạt động' }],
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
      customerTo: 0,
      isSelecting: false,
      fileExport: null,
      parsed: false,
      dataImport: [],
      customerSelected: new Customer(),
      dialogAddCustomer: false,
      dialogEditCustomer: false,
      moment: moment,
      params: {},
      userCurrent: JSON.parse(localStorage.getItem('user')),
      isLoading: false,
      customerExportFailValid: []
    }
  },
  watch: {
    page(value) {
      if (!this.parsed) {
        this.getCustomers()
      } else {
        if (value === 1) {
          this.customerFrom = 1;
          this.customerTo = this.itemsPerPage;
        } else if (value === this.pageLength) {
          this.customerFrom = this.itemsPerPage * (value-1) + 1;
          this.customerTo = this.totalCustomer;
        } else {
          this.customerFrom = this.itemsPerPage * (value-1) + 1;
          this.customerTo = this.customerFrom + this.itemsPerPage - 1;
        }

      }
    },
    customers(value) {
      if (this.parsed) {
        this.totalCustomer = value.length;
        this.pageLength = Math.ceil(this.totalCustomer / this.itemsPerPage)
      }
    },
    itemsPerPage(value) {
      if (!this.parsed) {
        this.page = 1;
        this.getCustomers()
      } else {
        this.page = 1;
        this.totalCustomer = this.customers.length;
        this.pageLength = Math.ceil(this.totalCustomer / value)
      }
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
  computed: {
    showError(arrError) {
        return arrError.join(',')
    }
  },
  created() {
    this.getCustomers();
  },
  methods: {
    async getCustomers() {
      try {
        this.loadingTable = true;
        this.params =  {
          page: this.page,
          perPage: this.itemsPerPage,
          customer_name: this.search.name,
          email: this.search.email,
          address: this.search.address,
          isActive: this.search.isActive,
          isSearch: this.isSearch,
        }
        const response  = await ServiceCustomer.getCustomers(this.params);
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
      if(this.parsed) {
        await Toast.show('warning', 'Không thể tìm kiếm trên file CSV!');
        return;
      }

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
    showDialogAddCustomer(customer) {
      if (!this.userCurrent.is_admin) {
        Toast.show('warning', i18n.t('permission.not'));
        return;
      }
      this.customerSelected = customer;
      this.dialogAddCustomer = true;
    },
    showDialogEditCustomer(customer) {
      if (this.userCurrent.group_role === i18n.t('group_role.reviewer')) {
        Toast.show('warning', i18n.t('permission.not'));
        return;
      }
      this.customerSelected = customer;
      this.dialogEditCustomer = true;
    },
    hasSearch() {
      if (this.search.name || this.search.email || this.search.address || (this.search.isActive === 1 || this.search.isActive === 0)) {
        return true
      }
      return false
    },
    async onFileChanged(e) {
      if (!(e.target && e.target.files && e.target.files[0])) {
        return
      }
      this.fileExport = e.target.files[0]
      this.parseFileImport();
    },

    handleFileImport(e) {
      if (this.parsed) {
        this.getCustomers();
        this.parsed = false
        this.$refs.uploader.value = null;
        return;
      }
      this.isSelecting = true;
      window.addEventListener('focus', () => {
        this.isSelecting = false
      }, { once: true });
      // Trigger click on the FileInput
      this.$refs.uploader.click();
    },

     parseFileImport() {
      const dataFormat = [];
      const dataExportFailValid = [];
      this.isSelecting = true;
      this.loadingTable = true;
      Papa.parse( this.fileExport, {
        header: true,
        skipEmptyLines: true,
        encoding: "utf-8",
        step: async function (row, parser) {
          let statusRow = true;
          if (row.errors.length === 0) {
            row.data.statusRow = true;
          }
          const data = Object.values(row.data);
          const customer_name = data[0];
          const email = data[1];
          const tel_num = data[2];
          const address = data[3];
          let is_added= false;
          const messages_err = [];
          const validateEmail = new RegExp('^\\w+([\\.-]?\\w+)*@\\w+([\\.-]?\\w+)*(\\.\\w{2,3})+$')
          const validatePhone = new RegExp('(84|0[3|5|7|8|9])+([0-9]{8})\\b');
          if (!validatePhone.test(tel_num)) {
            statusRow = false;
            messages_err.push(i18n.t('roles.malformed', { field: i18n.t('field.phone')}))
          }
          if (!validateEmail.test(email)) {
            statusRow = false;
            messages_err.push(i18n.t('roles.malformed', { field: i18n.t('field.email')}))
          }
          if (customer_name.length < 5) {
            statusRow = false;
            messages_err.push(i18n.t('roles.min_length', { field: i18n.t('field.name'), length: 5}))
          }
          if (!address || address === '') {
            statusRow = false;
            messages_err.push(i18n.t('roles.min_length', { field: i18n.t('field.address'), length: 5}))
          }
          if (statusRow) {
            parser.pause();
            try {
              const response = await ServiceCustomer.addCustomer({
                "customer_name" : customer_name,
                "email" : email,
                "address" : address,
                "tel_num" : tel_num,
                "is_active" : 1,
              })
              if (response.statusCode) {
                is_added = true
              }
            } catch (e) {
              if (e.status && e.status === Number(i18n.t('STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'))) {
                const errors = e.data.messages;
                if (errors.email) {
                  messages_err.push(i18n.t('roles.email'))
                }
              }
            }
            finally {
              parser.resume();
            }
          }
          if (!is_added) {
            dataExportFailValid.push({
              "customer_name" : customer_name,
              "email" : email,
              "address" : address,
              "tel_num" : tel_num
            })
          }

          dataFormat.push({
            "customer_name" : customer_name,
            "email" : email,
            "address" : address,
            "tel_num" : tel_num,
            "status_row" : statusRow,
            "is_active" : 1,
            'is_added' : is_added,
            'messages_fail': messages_err
          })
        },
        complete: function ( results ) {
          this.customers = dataFormat;
          this.parsed = true;
          this.loadingTable = false;
          this.isSelecting = false;
          if (dataExportFailValid.length > 0) {
            this.customerExportFailValid = dataExportFailValid;
            Swal.fire({
              title: 'Có vài dữ liệu bị lỗi?',
              html: 'Khi lưu lại những dữ liệu bị lỗi sẽ được lưu ra một file csv khác',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Lưu',
              cancelButtonText: 'Không'
            }).then(async (result) => {
              if (result.isConfirmed) {
                console.log(this.customerExportFailValid)
                this.funcExportFileCustomer(this.customerExportFailValid, 'fail_customer')
              }
            })
          }

        }.bind(this)
      });
    },

    async exportCustomer () {
      if (this.parsed) {
        this.funcExportFileCustomer(this.customerExportFailValid, 'fail_customer')
        return;
      }
      try {
        if (this.userCurrent.group_role === i18n.t('group_role.reviewer')) {
          await Toast.show('warning', i18n.t('permission.not'));
          return;
        }
        this.isLoading = true;
        const response  = await ServiceCustomer.getCustomers(this.params);
        if (response && response.statusCode) {
          const customers = response.data
          if (customers.data.length > 0) {
            this.funcExportFileCustomer(customers.data, `customer_from_${customers.from}_to_${customers.to}`)
          } else {
            await Toast.show('warning', i18n.t('notification.not_record_to_export'));
          }
        }
      } catch (e) {
        await Toast.show('warning', i18n.t('notification.export_fail'));
      } finally {
        this.isLoading = false;
      }

    },

    funcSuccess(customerNew) {
      if (!this.parsed) {
        this.getCustomers()
      } else {
        const indexUserNew = this.customers.findIndex(customer => customer.email === customerNew.email)
        if (indexUserNew !== -1) this.customers[indexUserNew].is_added = 1;
      }
    },

    funcExportFileCustomer(customersExport, fileName) {
      const data = customersExport.map(customer => [
        customer.customer_name,
        customer.email,
        customer.tel_num,
        customer.address
      ]);
      const fields = [i18n.t('field_csv.name'), i18n.t('field_csv.email'), i18n.t('field_csv.phone'), i18n.t('field_csv.address')];
      const csv = Papa.unparse({
        data,
        fields
      });
      const blob = new Blob([csv]);
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob, { type: 'text/plain' });
      const dayNow = this.moment.now();
      const formatDayNow = this.moment(dayNow).format('DD_MM_YYYY_HH_mm')
      a.download = `${fileName}_date_${formatDayNow}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      Toast.show('success', i18n.t('notification.export_success'));
    }

  }
}
</script>

<style scoped>

</style>
