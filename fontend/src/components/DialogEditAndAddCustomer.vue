<template>
  <v-dialog
      v-model="dialog"
      persistent
      max-width="600px"
  >
    <v-card>
      <v-card-title>
        <span class="text-h5">{{this.type === 'edit' ? 'Chỉnh sửa khách hàng' : 'Thêm khách hàng mới'}}</span>
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
                    v-model="customer.customer_name"
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
                    v-model="customer.email"
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
                    v-model="customer.tel_num"
                    :error-messages="errorsPhone"
                    :rules="phoneRules"
                    label="Điện thoại"
                    placeholder="Điện thoại"
                    type="text"
                    outlined
                    dense
                    required
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="pb-0">
                <v-text-field
                    v-model="customer.address"
                    :rules="addressRules"
                    label="Địa chỉ"
                    placeholder="Địa chỉ"
                    outlined
                    type="text"
                    dense
                    required
                ></v-text-field>
              </v-col>
              <v-col
                  cols="12"
                  sm="12"
              >
                <v-select
                    :items="listStatus"
                    v-model="customer.is_active"
                    :rules="isActiveRules"
                    label="Trạng thái"
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
import {ServiceCustomer} from "@/service/service.customer";
import {Toast} from "@/utils/toast";
import i18n from '@/plugins/i18n'

export default {
  name: "DialogEditAndAddCustomer",
  props: ['dialog', 'type', 'customerSelected'],
  emits: [ 'close', 'loadData'],
  data() {
    return {
      customer: Object.assign({}, this.customerSelected),
      valid: true,
      listStatus: [{ value: 0 , text :  i18n.t("status.pause")  }, { value: 1 , text : i18n.t("status.is_active") }],
      emailRules: [
        v => !!v || i18n.t('roles.required', { field: i18n.t('field.email')}),
        v => /.+@.+\..+/.test(v) || i18n.t('roles.malformed', { field: i18n.t('field.email')}),
      ],
      nameRules: [
        v => !!v || i18n.t('roles.required', { field: i18n.t('field.name')}),
        v => (v && v.length > 5) || i18n.t('roles.min_length', { field: i18n.t('field.name'), length: 5}),
      ],
      addressRules: [
        v => !!v || i18n.t('roles.required', { field: i18n.t('field.address')}),
      ],
      isActiveRules: [
        v => v !== undefined || i18n.t('roles.required', { field: i18n.t('field.is_active')})
      ],
      phoneRules: [
        v => v !== undefined || i18n.t('roles.required', { field: i18n.t('field.phone')}),
        v => /^(84|0[3|5|7|8|9])+([0-9]{8})\b$/.test(v) || i18n.t('roles.malformed', { field: i18n.t('field.phone')})
      ],
      errorsPhone: null,
      errorsEmail: null

    }
  },
  watch: {
    'customer.tel_num' (value) {
      if (this.errorsPhone) this.errorsPhone = null;
    },
    'customer.email' (value) {
      if (this.errorsEmail) this.errorsEmail = null;
    }
  },
  methods: {
    closeDialog () {
      this.$emit('close');
    },
    async save() {
      const isValid = this.$refs.form.validate()
      if (isValid) {
          if (this.type === 'add') {
              try {
                const response = await ServiceCustomer.addCustomer(this.customer)
                if (response.statusCode) {
                  this.$emit('loadData', response.data);
                  this.$emit('close');
                  await Toast.show('success', i18n.t('notification.add_success'));
                }
              } catch (e) {
                if (e.status && e.status === Number(i18n.t('STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'))) {
                  const errors = e.data.messages;
                  this.errorsEmail = errors.email;
                  if (errors.permission) {
                    await Toast.show('error', errors.permission);
                  }
                }
              }
          } else {
            try {
              const response = await ServiceCustomer.updateCustomer(this.customer)
              if (response.statusCode) {
                this.$emit('loadData');
                await this.$emit('close');
                await Toast.show('success', i18n.t('notification.add_success'));
              }
            } catch (e) {
              if (e.status && e.status === Number(i18n.t('STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'))) {
                const errors = e.data.messages;
                this.errorsEmail = errors.email;
                if (errors.detail) {
                  await Toast.show('error', errors.detail);
                }
              }
            }
          }
      }
    }
  }
}
</script>

<style scoped>

</style>