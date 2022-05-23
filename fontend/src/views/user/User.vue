<template src="./UserTemplate.html"></template>
<style src="./style.css"></style>
<script>
import Header from '@/components/Header'
import {ServiceUser} from "@/service/service.user";
import {User, Role} from '@/utils/class.user'
import DialogEditUser from "@/components/DialogEditUser";
import Swal from 'sweetalert2'
import {Toast} from "@/utils/toast";
export default {
  components: {Header, DialogEditUser},
  data() {
    return {
      loadingTable: false,
      loggedIn: false,
      namePage : 'Products',
      groups: ['Admin', 'Editor', 'Reviewer'],
      statusUser: [{ value: 0 , text : 'Tạm khóa'}, { value: 1 , text : 'Đang hoạt động'}],
      search: {
        name: '',
        email: '',
        group: '',
        status: '',
      },
      totalUser: 0,
      pageLength: 0,
      pageCount: 0,
      itemsPerPage: 10,
      page: 1,
      headers: [
        {
          text: '#',
          align: 'start',
          sortable: false,
          value: 'id',
          class: "white--text"
        },
        { text: 'Họ tên', value: 'name', class: "white--text", sortable: false},
        { text: 'Email', value: 'email', class: "white--text", sortable: false},
        { text: 'Nhóm', value: 'group_role', class: "white--text", sortable: false},
        { text: 'Trạng thái', value: 'is_active', class: "white--text", sortable: false},
        { text: 'Hành động', value: 'act', class: "white--text", sortable: false},
      ],
      users: [],
      isSearch: 0,
      userFrom: 0,
      userTo: 0,
      perPages: [10, 15, 20],
      dialogLockOrUnlock: false,
      dialogDeleteUser: false,
      dialogEditUser: false,
      dialogAddUser: false,
      userSelected: new User(),
      notify: false,
      notifyText: '',
      notifyColor: '',
      userCurrent: JSON.parse(localStorage.getItem('user')),
      role: new Role()
    }
  },
  watch: {
      page() {
        this.getListUser()
      },
      itemsPerPage() {
        this.getListUser()
      }
  },
  created() {
    this.getListUser()
  },
  methods: {
    async getListUser() {
      try {
        this.loadingTable = true;
        const params =  {
          page: this.page,
          perPage: this.itemsPerPage,
          name: this.search.name,
          email: this.search.email,
          group: this.search.group,
          status: this.search.status,
          isSearch: this.isSearch,
        }
        const response = await ServiceUser.getUsers(params)
        if (response && response.statusCode) {
          const users = response.data;
          this.pageLength = users.last_page;
          this.users = users.data;
          this.totalUser = users.total;
          this.userFrom = users.from;
          this.userTo = users.to;
        }
      } catch (e) {
        if (e.response && e.response.status === 401) {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            this.$router.push('login')

        }
      }
      finally {
        this.loadingTable = false;
      }
    },

    async searchUser() {
      if (this.hasSearch()) {
          this.isSearch = true;
          this.page = 1
          await this.getListUser();
      } else {
        await Toast.show('warning', 'Vui lòng nhập vào thông tin để tìm kiếm!');
      }
    },

    async clearSearch() {
      if (this.hasSearch()) {
        this.search.group = '';
        this.search.status = '';
        this.search.name = '';
        this.search.email = '';
        this.isSearch = 0;
        await this.getListUser();
      }
    },

    confirmLockOrUnlockUser(user) {
      if (!this.hasPermission(user)) {
        Toast.show('warning', 'Người dùng này không thể khóa!');
        return;
      }
      Swal.fire({
        title: 'Bạn có chắc muốn khóa người dùng này không?',
        html: `Người dùng: <strong>${user.name}</strong> sau khi khóa sẽ không đăng nhập được!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Khóa',
        cancelButtonText: 'Hủy'
      }).then(async (result) => {
        if (result.isConfirmed) {
          this.userSelected = user;
          await this.lockOrUnlockUser();
        }
      })
    },

    confirmDeleteUser(user) {
      if (!this.hasPermission(user)) {
        Toast.show('warning', 'Người dùng này không thể xóa!');
        return;
      }
      Swal.fire({
        title: 'Bạn có chắc muốn xóa người dùng này không?',
        html: `Người dùng: <strong>${user.name}</strong> sau khi xóa sẽ không đăng nhập được!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
      }).then(async (result) => {
        if (result.isConfirmed) {
          this.userSelected = user;
          await this.deleteUser();
        }
      })
    },

    async lockOrUnlockUser() {
      try {
        const type = this.userSelected.is_active == 1 ? 'lock' : 'unlock';
        const response = await ServiceUser.blockAndUnBlockUser({idUserBlock: this.userSelected.id, type: type});
        if (response.statusCode) {
          const idxUserSelected = this.users.findIndex(user => user.id === this.userSelected.id);
          this.users[idxUserSelected].is_active = type === 'lock' ? 0 : 1;
          await Toast.show('success', `Đã ${type === 'lock' ? 'khóa' : 'mở'} ${this.userSelected.name} thành công`)
          return;
        }
        await Toast.show('error', response.messages)
      } catch (e) {
        console.log(e)
      }
      finally {
        this.dialogLockOrUnlock = false;
      }
   },

    async deleteUser() {
      try {
        const response = await ServiceUser.deleteUser({idUserDelete: this.userSelected.id});
        if (response.statusCode) {
          await this.getListUser();
          await Toast.show('success', 'Đã xóa thành công!')
          return;
        }
        await Toast.show('error', response.messages)
      } catch (e) {
        console.log(e)
      }
      finally {
        this.dialogDeleteUser = false;
      }
    },

    showDialogEditUser (user) {
      if (!this.hasPermissionEditUser(user)) {
        Toast.show('warning', 'Bạn không có quyền thực hiện tính năng!');
        return;
      }
      this.userSelected = user;
      this.dialogEditUser = true;
    },

    showDialogAddUser () {
      if (this.userCurrent.group_role !== this.role.admin) {
        Toast.show('warning', 'Bạn không có quyền thực hiện tính năng!');
        return;
      }
      this.dialogAddUser = true;
      this.userSelected = new User()
    },

    hasPermission(user) {
      if (
          (user.is_admin && this.userCurrent.id !== 1)
          || user.id === this.userCurrent.id
          || !this.userCurrent.is_admin)
      {
          return false;
      }
      return true;
    },

    hasPermissionEditUser(user) {
      if ( (this.userCurrent.group_role === 'Reviewer' && user.group_role !== 'Reviewer')
          || (this.userCurrent.group_role === 'Editor' && user.is_admin )
          || (this.userCurrent.group_role === 'Editor' && user.group_role === 'Editor' && user.id !== this.userCurrent.id)
          || (user.is_admin && user.id === 1 && this.userCurrent.id !== 1)
          || (user.is_admin && user.id !== 1 && this.userCurrent.id !== user.id && this.userCurrent.id !== 1)
      ) {
          return false;
      }
      return true;
    },
    hasSearch() {
      if (this.search.name || this.search.email || this.search.group || (this.search.status === 1 || this.search.status === 0)) {
        return true
      }
      return false
    },

    addSuccess (userUpdated) {
      const findIndex = this.users.findIndex(user => user.id === userUpdated.id)
      this.users[findIndex] = userUpdated;
      console.log(this.users[findIndex])
    }
  }
}

</script>


