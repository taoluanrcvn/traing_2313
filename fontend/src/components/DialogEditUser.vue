<template>
  <v-dialog
          v-model="dialog"
          persistent
          max-width="600px"
      >
        <v-card>
          <v-card-title>
            <span class="text-h5">{{this.type === 'edit' ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới'}}</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-form
                  ref="form"
                  v-model="valid"
                  lazy-validation>
                <v-row>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="user.name"
                        :rules="nameRules"
                        label="Họ tên"
                        placeholder="Nhập họ và tên"
                        dense
                        outlined
                        required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="user.email"
                        label="Email"
                        :rules="emailRules"
                        required
                        placeholder="Nhập E-mail"
                        dense
                        outlined
                        :error-messages="errorsEmail"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="password"
                        label="Mật khẩu"
                        placeholder="Nhập mật khẩu"
                        type="password"
                        outlined
                        dense
                        required
                        :rules="passwordRules"
                        :error-messages="errorsPassword"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="passwordConfirm"
                        label="Xác nhận mật khẩu"
                        placeholder="Nhập mật khẩu xác nhận"
                        outlined
                        type="password"
                        dense
                        required
                        :error-messages="errorsConfirmPassword"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      cols="12"
                      sm="12"
                  >
                    <v-select
                        v-model="user.group_role"
                        :items="groups"
                        :rules="groupRules"
                        label="Nhóm"
                        dense
                        outlined
                    ></v-select>
                  </v-col>
                  <v-col
                      cols="12"
                      sm="6"
                  >
                    <v-select
                        v-model="user.is_active"
                        :items="statusUser"
                        label="Trạng thái"
                        hide-details
                        dense
                        outlined
                    ></v-select>
                  </v-col>
                </v-row>
              </v-form>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
                color="error"
                text
                @click="closeDialog"
            >
              Đóng
            </v-btn>
            <v-btn
                color="blue darken-1"
                text
                @click="save()"
                :disabled="!valid"
            >
              {{this.type === 'edit' ? 'Lưu' : 'Thêm'}}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
</template>

<script>
import {ServiceUser} from "@/service/service.user";

export default {
  name: "DialogEditUser",
  props: ['dialog', 'group', 'userSelected', 'type'],
  emits: ['clickSubmit', 'close', 'failAdd'],
  data() {
    return {
      user: this.userSelected,
      groups: ['Admin', 'Editor', 'Reviewer'],
      statusUser: [{ value: 0 , text : 'Tạm khóa'}, { value: 1 , text : 'Đang hoạt động'}],
      emailRules: [
        v => !!v || 'E-mail không được để trống!',
        v => /.+@.+\..+/.test(v) || 'E-mail không hợp lệ!',
      ],
      passwordRules: [
        v => !!v || 'Mật khẩu không được để trống!',
        v => (v && v.length > 5) || 'Mật khẩu phải hơn 5 ký tự!',
      ],
      groupRules: [
        v => !!v || 'Không được để trống!',
      ],
      nameRules: [
        v => !!v || 'Không được để trống!',
        v => (v && v.length > 5) || 'Mật khẩu phải hơn 5 ký tự!',
      ],
      valid: true,
      errorsPassword: null,
      errorsEmail: null,
      errorsConfirmPassword: null,
      password: null,
      passwordConfirm: null
    }
  },
  created() {
    if (this.type === 'edit') {
      this.user.password = 'default'
    }
  },
  watch: {
    'user.email' () {
      if (this.errorsEmail) {
        this.errorsEmail = null
      }
    },
    passwordConfirm (value) {
     this.checkPassword(this.password, value)
    },
    password (value) {
      this.checkPassword(value, this.passwordConfirm)
    }

  },
  methods: {
    closeDialog () {
      this.$emit('close');
    },
    async save() {
      const isValid = this.$refs.form.validate()
      if (isValid) {
        if (this.type === 'add'){
          try {
            this.user.password = this.password;
            this.user.passwordConfirm = this.passwordConfirm;
            const response = await ServiceUser.addUser(this.user)
              if (response.statusCode) {
                this.$emit('close');
                this.notification('success', 'Đã thêm thành công');
                await this.getListUser();
                return;
              } else {
                const errors = response.messages;
                this.errorsEmail = errors.email
                if (errors.role) {
                  this.notification('error', errors.role);
                }
              }
          } catch (e) {

          }

        }
      }
    },
    checkPassword(password, passwordConfirm) {
      if (password !== passwordConfirm && password && passwordConfirm) {
        this.errorsConfirmPassword = 'Mật khẩu không khớp';
        this.errorsPassword = 'Mật khẩu không khớp';
      } else {
        this.errorsConfirmPassword = null;
        this.errorsPassword = null;
      }
    }
  }
}
</script>

<style scoped>

</style>