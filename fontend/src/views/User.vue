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
              :items="list_group"
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
              :items="list_status"
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
            @click="addUser()"
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
                    @click="deleteUser(item.id)"
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
                    color="error"
                    v-bind="attrs"
                    v-on="on"
                    @click="blockUser(item.id)"
                >
                  <v-icon> mdi-account-key</v-icon>
                </v-btn>
              </template>
              <span class="hi">Khóa</span>
            </v-tooltip>
          </template>
        </v-data-table>
        <div class="text-center pt-2" v-if="totalUser > itemsPerPage">
          <span class="overline">Hiển thị từ {{userFrom}} ~ {{userTo}} trong tổng số {{totalUser}} user</span>
          <v-pagination
              v-model="page"
              :length="pageLength"
          ></v-pagination>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Header from '@/components/Header'
import {ServiceUser} from "@/service/service.user";


export default {
  components: {Header},
  data() {
    return {
      loadingTable: false,
      loggedIn: false,
      namePage : 'Products',
      list_group: ['Admin', 'Editor', 'Reviewer'],
      list_status: [{ value: 0 , text : 'Tạm khóa'}, { value: 1 , text : 'Đang hoạt động'}],
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
      perPages: [10, 15, 20]
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
    // console.log(localStorage.getItem('token'))
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
          console.log(e)
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

    deleteUser(id) {

    },

   async blockUser(id) {
     const response = await ServiceUser.blockUser({idUserBlock: id})
     console.log(response)
   },

    addUser(id) {

    }
  }
}
function getData (n) {
  const result = [];
  for (let i = 0 ; i < n ; i++) {
    const rndInt = Math.floor(Math.random() * 2) + 1
    const group_role = ['Admin', 'Editor', 'Reviewer']
    const user = {
      id: i,
      name: `Nguyễn văn ${i}`,
      email: `email${i}@gmail.com`,
      group_role: group_role[rndInt],
      is_active: 'Đang hoạt động',
    }
    result.push(user);
  }
  return result;
}
</script>

<style scoped>
@import '../assets/css/users.css';
</style>
