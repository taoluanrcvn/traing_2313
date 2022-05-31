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
                        label="Họ tên*"
                        placeholder="Nhập họ và tên"
                        dense
                        outlined
                        required
                        maxlength="255"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="user.email"
                        label="Email*"
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
                        :label="this.type === 'add' ? 'Mật khẩu*' : 'Mật khẩu'"
                        placeholder="Nhập mật khẩu"
                        type="password"
                        outlined
                        dense
                        required
                        :rules="passwordRules"
                        :error-messages="errorsPassword"
                        :append-icon="showPws ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showPws = !showPws"
                        :type="showPws ? 'text' : 'password'"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="passwordConfirm"
                        placeholder="Nhập mật khẩu xác nhận"
                        :label="this.type === 'add' ? 'Xác nhận mật khẩu*' : 'Xác nhận mật khẩu'"
                        outlined
                        type="password"
                        dense
                        required
                        :error-messages="errorsConfirmPassword"
                        :append-icon="showPws ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showPws = !showPws"
                        :type="showPws ? 'text' : 'password'"
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
                        label="Nhóm*"
                        :error-messages="errorsGroup"
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
                        :rules="statusRules"
                        label="Trạng thái*"
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
import {Toast} from "@/utils/toast";
import {Role} from "@/utils/class.user";
import i18n from "@/plugins/i18n";

export default {
  name: "DialogEditUser",
  props: ['dialog', 'group', 'userSelected', 'type', 'userCurrent'],
  emits: [ 'close', 'loadData'],
  data() {
    return {
      user: Object.assign({}, this.userSelected),
      groups: ['Admin', 'Editor', 'Reviewer'],
      statusUser: [{ value: 0 , text : 'Tạm khóa'}, { value: 1 , text : 'Đang hoạt động'}],
      emailRules: [
        v => !!v || 'E-mail không được để trống!',
        v => /.+@.+\..+/.test(v) || 'E-mail không hợp lệ!',
      ],
      passwordRules: [
        v => !!v || 'Mật khẩu không được để trống!',
        v => (v && v.length > 5) || 'Mật khẩu phải hơn 5 ký tự!'
      ],
      groupRules: [
        v => !!v || 'Không được để trống!',
      ],
      nameRules: [
        v => !!v || 'Không được để trống!',
        v => (v && v.length > 5) || 'Mật khẩu phải hơn 5 ký tự!',
      ],
      statusRules: [
        v => v !== undefined && v !== null || i18n.t('roles.status.required')
      ],
      valid: true,
      errorsPassword: null,
      errorsEmail: null,
      errorsConfirmPassword: null,
      password: this.type === 'edit' ? 'Abc123test' : null,
      passwordConfirm: this.type === 'edit' ? 'Abc123test' : null,
      role: new Role(),
      errorsGroup: null,
      showPws: false
    }
  },
  created() {
    if (this.type === 'edit') {
      this.user.password = 'default'
    }
  },
  watch: {
    'user.group_role' (value) {
      if (value === this.role.admin && this.userCurrent.id !== 1) {
        this.errorsGroup = 'Bạn không có quyền chọn nhóm này'
      } else {
        this.errorsGroup = null
      }
    },
    'user.email' (value) {
      console.log(value.includes(' '))
      if (value.includes(' ')) {
        this.errorsEmail = 'Email không được chứa khoảng trắng'
        // this.user.email = value.replace(/^\s+|\s+$/gm,'')
      } else  {
        if (this.errorsEmail) {
          this.errorsEmail = null
        }
      }
    },
    passwordConfirm (value) {
      this.checkPassword(this.password, value)
    },
    password (value) {
      if (value) {
        if (value.includes(' ')) {
          this.errorsPassword = 'Mật khẩu phải có chữ hoa, thường, số và không chứa khoảng trắng!';
        }
        this.checkPassword(value, this.passwordConfirm)
        const regex = new RegExp('(?=[A-Za-z0-9]+$)^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{6,}).*$');
        if (!regex.test(value)) {
          this.errorsPassword = 'Mật khẩu phải có chữ hoa, thường, số và không chưa khoảng trắng!';
        } else {
          this.checkPassword(value, this.passwordConfirm)
          this.errorsPassword = null;
        }
      }
    },

  },
  methods: {
    closeDialog () {
      this.$emit('close');
    },
    async save() {
      const isValid = this.$refs.form.validate()
      if (isValid) {
        if (this.type === 'add') {
         if (!this.passwordConfirm) {
           this.errorsConfirmPassword = 'Vui lòng nhập mật khẩu xác nhận';
         }
          try {
            this.user.password = this.password;
            this.user.passwordConfirm = this.passwordConfirm;
            const response = await ServiceUser.addUser(this.user)
            if (response.statusCode) {
              this.$emit('close');
              this.$emit('loadData', response.data);
              await Toast.show('success', 'Đã thêm thành công');
            }
          } catch (e) {
            if (e.status && e.status === 422) {
              const errors = e.data.messages;
              this.errorsEmail = errors.email;
              this.errorsGroup = errors.group_role;
              if (errors.role) {
                await Toast.show('error', errors.role);
              }
            }
          }
        }
        else {
          if (this.password !== 'Abc123test') {
            this.user.password = this.password;
          } else {
            delete this.user.password;
          }
          try {
            const response = await ServiceUser.updateUser(this.user);
            if (response.statusCode) {
              this.$emit('close');
              this.$emit('loadData', response.data);
              await Toast.show('success', 'Cập nhật thành công');
            }
          } catch (e) {
            if (e.status && e.status === 422) {
              const errors = e.data.messages;
              this.errorsEmail = errors.email;
              this.errorsGroup = errors.group_role;
              if (errors.role) {
                await Toast.show('error', errors.role);
              }
            }
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
    },
  }
}
</script>

<style scoped>

</style>
