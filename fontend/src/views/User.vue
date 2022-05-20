<template>
  <v-container fluid>
    <v-row class="w-100 mb-4">
      <Header/>
    </v-row>

    <v-form>
      <v-row class="d-flex justify-center mt-4">
        <v-col
            cols="12"
            md="4"
            sm="4"
            lg="2"
        >
          <v-text-field
              v-model="search.name"
              label="Tên"
              hide-details
              dense
              outlined
          ></v-text-field>
        </v-col>

        <v-col
            cols="12"
            md="4"
            sm="4"
            lg="2"
        >
          <v-text-field
              v-model="search.email"
              label="Email"
              hide-details
              dense
              outlined
          ></v-text-field>
        </v-col>

        <v-col
            cols="12"
            md="4"
            sm="4"
            lg="2"
        >
          <v-select
              v-model="search.group"
              :items="groups"
              label="Nhóm"
              hide-details
              dense
              outlined
          ></v-select>
        </v-col>

        <v-col
            cols="12"
            md="4"
            sm="4"
            lg="2"
        >
          <v-select
              v-model="search.status"
              :items="statusUser"
              label="Trạng thái"
              hide-details
              dense
              outlined
          ></v-select>
        </v-col>

        <v-col
            xs="6"
            sm="2"
            lg="1"
            class="d-flex justify-center"
        >
          <v-btn
              depressed
              color="primary"
              @click="searchUser()"
          >
            <v-icon left>
              mdi-magnify
            </v-icon>
            Tìm kiếm
          </v-btn>
        </v-col>

        <v-col
            xs="6"
            sm="2"
            lg="1"
            class="d-flex justify-center"
        >
          <v-btn
              depressed
              color="error"
              @click="clearSearch()"
          >
            <v-icon left>
              mdi-magnify-minus
            </v-icon>
            Xóa tìm kiếm
          </v-btn>
        </v-col>
      </v-row>
    </v-form>

    <v-row class="mt-5">
      <v-col cols="12">
        <v-btn
            depressed
            color="success"
            @click="showDialogUser()"
        >
          <v-icon left>
            mdi-account-plus
          </v-icon>
          Thêm user
        </v-btn>
        <div class="float-end">
          <span class="pl-4">Hiện thị tối đa</span>
          <v-menu
              rounded
              offset-y
          >
            <template v-slot:activator="{ attrs, on }">
              <v-btn
                  color="primary"
                  class="black--textt ml-3 elevation-0"
                  v-bind="attrs"
                  v-on="on"
                  small
              >
                {{ itemsPerPage }}
              </v-btn>
            </template>

            <v-list>
              <v-list-item
                  v-for="item in perPages"
                  :key="item"
                  link
              >
                <v-list-item-title v-text="item" @click="itemsPerPage = item"></v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>

      </v-col>
      <v-col cols="12">
        <v-data-table
            :headers="headers"
            :items="users"
            :page.sync="page"
            :items-per-page="itemsPerPage"
            hide-default-footer
            class="elevation-1 "
            @page-count="pageCount = $event"
            :loading="loadingTable"
            loading-text="Đang tải...."
        >
          <template v-slot:item.id="{ item , index }">
            {{userFrom+index}}
          </template>
          <template v-slot:item.is_active="{ item }">
              <span v-if="item.is_active" class="success--text">
                  Đang hoạt động
              </span>
              <span v-else class="red--text">
                  Tạm khóa
              </span>
          </template>
          <template v-slot:item.act="{ item }">
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    icon
                    color="primary"
                    v-bind="attrs"
                    v-on="on"
                    @click="showDialogEditUser(item)"
                >
                  <v-icon>mdi-clipboard-account</v-icon>
                </v-btn>
              </template>
              <span>Sửa</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    icon
                    color="secondary"
                    v-bind="attrs"
                    v-on="on"
                    @click="confirmDeleteUser(item)"
                >
                  <v-icon>mdi-account-remove</v-icon>
                </v-btn>
              </template>
              <span>Xóa</span>
            </v-tooltip>

            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    icon
                    :color="item.is_active === 1 ? 'error' : 'success'"
                    v-bind="attrs"
                    v-on="on"
                    @click="confirmBlockUser(item)"
                >
                  <v-icon>{{item.is_active === 0 ? 'mdi-account-key' : 'mdi-account-alert'}}</v-icon>
                </v-btn>
              </template>
              <span class="hi">{{item.is_active === 0 ? 'Mở khóa' : 'Khóa'}}</span>
            </v-tooltip>
          </template>
        </v-data-table>
        <div class="text-center pt-2" v-if="totalUser > itemsPerPage">
          <span class="overline">Hiển thị từ {{userFrom}} ~ {{userTo}} trong tổng số {{totalUser}} người dùng</span>
          <v-pagination
              v-model="page"
              :length="pageLength"
          ></v-pagination>
        </div>
      </v-col>
    </v-row>
    <DialogConfirm
        title="Bạn có chắc muốn khóa người dùng này không?"
        titleSub="Người dùng sẽ bị khóa và sẽ không đăng nhập được!"
        @clickAccept="lockOrUnlockUser()"
        :dialog="dialogLockOrUnlock"
        @close="dialogLockOrUnlock = false"
    >
    </DialogConfirm>
    <DialogConfirm
        title="Bạn có chắc muốn xóa người dùng này không?"
        titleSub="Người dùng này sẽ bị xóa!"
        @clickAccept="deleteUser()"
        :dialog="dialogDeleteUser"
        @close="dialogDeleteUser = false"
    >
    </DialogConfirm>
    <DialogEditUser
        v-if="dialogEditUser"
        :dialog="dialogEditUser"
        :userSelected="userSelected"
        @close="dialogEditUser = false"
        type="edit"
    ></DialogEditUser>
    <DialogEditUser
        v-if="dialogAddUser"
        :dialog="dialogAddUser"
        :userSelected="userSelected"
        @successAdd="statusAddUser()"
        @close="dialogAddUser = false"
        type="add"
    ></DialogEditUser>
    <v-snackbar
        v-model="notify"
        :color="notifyColor"
    >
      {{ notifyText }}

      <template v-slot:action="{ attrs }">
        <v-btn
            color="pink"
            text
            v-bind="attrs"
            @click="notify = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>

</template>

<script>
import Header from '@/components/Header'
import {ServiceUser} from "@/service/service.user";
import User from '@/utils/class.user'
import DialogConfirm from "@/components/DialogConfirm";
import DialogEditUser from "@/components/DialogEditUser";

export default {
  components: {Header, DialogConfirm, DialogEditUser},
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
        { text: 'Họ tên', value: 'name', class: "white--text"},
        { text: 'Email', value: 'email', class: "white--text"},
        { text: 'Nhóm', value: 'group_role', class: "white--text"},
        { text: 'Trạng thái', value: 'is_active', class: "white--text" },
        { text: 'Hành động', value: 'act', class: "white--text" },
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
      userCurrent: JSON.parse(localStorage.getItem('user'))
    }
  },
  watch: {
      page() {
        this.getListUser()
      },
      itemsPerPage(per) {
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
        // xử lý status code 401 thì log-out
        if (e.response && e.response.status === 401) {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            this.$router.push('login')
          console.log(this.auth.check());

        }
        console.log(e.status)
      }
      finally {
        this.loadingTable = false;
      }
    },

    async searchUser() {
      if (this.search.name || this.search.email || this.search.group || (this.search.status === 1 || this.search.status === 0)) {
          this.isSearch = true;
          this.page = 1
          await this.getListUser();
      }

    },

    async clearSearch() {
      if (this.search.name || this.search.email || this.search.group || (this.search.status === 1 || this.search.status === 0)) {
        this.search.group = '';
        this.search.status = '';
        this.search.name = '';
        this.search.email = '';
        this.isSearch = 0;
        await this.getListUser();
      }
    },

    confirmBlockUser(user) {
      if (user.group_role === 'Admin') {
        this.notification('', 'Người dùng này không thể khóa!')
        return;
      }
      this.userSelected = user;
      this.dialogLockOrUnlock = true;
    },

    confirmDeleteUser(user) {
      if (user.group_role === 'Admin') {
        this.notification('', 'Người dùng này không thể xóa!')
        return;
      }
      this.userSelected = user;
      this.dialogDeleteUser = true;
    },

    async lockOrUnlockUser() {
      try {
        const type = this.userSelected.is_active == 1 ? 'lock' : 'unlock';
        const response = await ServiceUser.blockAndUnBlockUser({idUserBlock: this.userSelected.id, type: type});
        if (response.statusCode) {
          const idxUserSelected = this.users.findIndex(user => user.id === this.userSelected.id);
          this.users[idxUserSelected].is_active = type === 'lock' ? 0 : 1;
          this.notification('success', `Đã ${type === 'lock' ? 'khóa' : 'mở'} ${this.userSelected.name} thành công`);
          return;
        }
        this.notification('error', response.messages)
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
          this.notification('success', 'Đã xóa thành công');
          return;
        }
        this.notification('error', response.messages)
      } catch (e) {
        console.log(e)
      }
      finally {
        this.dialogDeleteUser = false;
      }
    },

    notification(type, messages) {
      this.notify = true;
      this.notifyText = messages;
      switch (type) {
        case 'error':
          this.notifyColor = 'error'
          break;
        case 'success':
          this.notifyColor = 'success'
          break;
       default:
         this.notifyColor = 'warning'
          break;
      }
    },

    showDialogEditUser (user) {
      this.userSelected = Object.assign({}, user);
      this.dialogEditUser = true;
    },

    showDialogUser () {
      this.dialogAddUser = true;
      this.userSelected = new User()
    },

    async statusAddUser(data) {
      this.notification('success', 'Đã thêm thành công');
      await this.getListUser();
    }
  }
}

</script>

<style scoped>
@import '../assets/css/users.css';
</style>
