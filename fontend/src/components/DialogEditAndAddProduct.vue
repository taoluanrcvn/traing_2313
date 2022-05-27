<template>
  <v-dialog
      v-model="dialog"
      persistent
      max-width="1000px"
  >
    <v-card>
      <v-card-title>
        <span class="text-h5">{{title}}</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form
              ref="form"
              v-model="valid"
              lazy-validation>
            <v-row>
              <v-col cols="6">
                <v-row>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="product.product_name"
                        label="Họ tên"
                        :label="$t('product.label.name')"
                        :rules="nameRules"
                        dense
                        outlined
                        required
                        :hide-details="valid"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-text-field
                        v-model="product.product_price"
                        :rules="priceRules"
                        :label="$t('product.label.price_from')"
                        type="number"
                        dense
                        outlined
                        prefix="$"
                        :hide-details="valid"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-textarea
                        v-model="product.description"
                        outlined
                        name="input-7-4"
                        :label="$t('product.label.deception')"
                        :hide-details="valid"
                    ></v-textarea>
                  </v-col>
                  <v-col cols="12" class="pb-0">
                    <v-select
                        :items="statusProducts"
                        v-model="product.is_sales"
                        :rules="statusRules"
                        :error-messages="errorStatus"
                        :label="$t('product.label.status')"
                        dense
                        outlined
                        :hide-details="valid"
                    ></v-select>
                  </v-col>
                  <v-col
                      cols="12"
                      sm="12"
                  >
                    <v-text-field
                        v-model="product.inventory"
                        :error-messages="errorInventory"
                        :rules="inventoryRules"
                        :label="$t('product.label.inventory')"
                        type="number"
                        dense
                        outlined
                        :hide-details="valid"
                        :disabled="product.is_sales == 2"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="6" class="d-flex flex-column justify-center align-center">
                <div
                    class="base-image-input"
                    :style="{ 'background-image': `url(${!isChooseImage ? urlImage : ''}${product.product_image})` }"
                    @click="chooseImage"
                >
                    <span
                        v-if="!product.product_image"
                        class="placeholder"
                    >
                      {{$t('product.label.image_text')}}
                    </span>
                    <input
                        class="file-input"
                        ref="fileInput"
                        type="file"
                        accept="image/png, image/jpeg, image/png, image/jpg"
                        @input="onSelectFile"
                    />
                </div>
                <v-btn
                    class="elevation-0 text-center mt-4"
                    color="error"
                    @click="clearFile"
                    v-if="product.product_image"
                >
                  Xóa file
                </v-btn>
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
import {Product} from "@/utils/class.user";
import i18n from "@/plugins/i18n";
import {Toast} from "@/utils/toast";
import {ServiceProduct} from "@/service/serve.product";

export default {
  name: "DialogEditAndAddProduct",
  props: ['dialog', 'productSelected', 'type', 'userCurrent', 'title', 'statusProducts'],
  emits: [ 'close', 'loadData'],
  data() {
    return {
      product: Object.assign(new Product(), this.productSelected),
      valid: true,
      imageData: null,
      nameRules: [
        v => !!v || i18n.t('roles.required', { field: i18n.t('field.name_product')}),
        v => (v && v.length > 5) || i18n.t('roles.min_length', { field: i18n.t('field.name_product'), length: 5}),
      ],
      priceRules: [
        v => v > 0 || i18n.t('roles.price_min_add'),
      ],
      statusRules: [
        v => v !== undefined && v !== null || i18n.t('roles.status.required')
      ],
      inventoryRules: [
        v => v >= 0 || i18n.t('roles.inventory'),
      ],
      errorsFile: null,
      errorStatus: null,
      errorInventory: null,
      imageDefault: 'http://localhost:8000/storage/images/production.png',
      urlImage: 'http://localhost:8000/storage/',
      isChooseImage: false,
    }
  },
  mounted() {
    this.product.is_sales = this.product.inventory === 0 ? 2 : this.product.is_sales
  },
  watch: {
    'product.is_sales'(value) {
        if (this.type === 'add' && value !== 1) {
          this.errorStatus = i18n.t('status.not_change_status')
        } else {
          this.errorStatus = null;
        }

        if (this.type === 'edit' && value === 2) {
          this.product.inventory = 0;
        }

        if ((value === 1 || value === 0) && this.product.inventory === 0) {
            this.product.inventory = this.productSelected.inventory
        }
      },
    'product.inventory'(value) {
      if (this.type === 'add' && value <= 0) {
        this.errorInventory = i18n.t('roles.inventory')
      } else {
        this.errorInventory = null
      }
      if (value == 0 && this.product.is_sales == 1) {
        this.errorInventory = i18n.t('roles.inventory')
      }
    },
    'product.product_image'(image) {
      // console.log(image)
    }
  },
  methods: {
    closeDialog() {
      this.$emit('close');
    },
    async save() {
      if (!this.product.product_image) {
        await Toast.show('warning', i18n.t('roles.image_required'));
        return;
      }
      const isValid = this.$refs.form.validate()
      if (isValid && this.product.product_image) {
          if (this.type === 'add') {
            try {
              const response = await ServiceProduct.addProduct(this.product)
              if (response.statusCode) {
                this.$emit('close');
                this.$emit('loadData', response.data);
                await Toast.show('success', i18n.t('notification.add_success'));
              }
            } catch (e) {
              if (e.status && e.status === Number(i18n.t('STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'))) {
                if (errors.detail) {
                  await Toast.show('error', errors.detail);
                }
              }
            }
          } else {
            try {
              if ((this.product.is_sales == 1 || this.product.is_sales == 0) && this.product.inventory <= 0) {
                this.errorInventory = i18n.t('roles.inventory')
                return
              }
              this.product.is_upload = this.isChooseImage;
              const response = await ServiceProduct.updateProduct(this.product)
              if (response.statusCode) {
                this.$emit('close');
                this.$emit('loadData', response.data);
                await Toast.show('success', i18n.t('notification.update_success'));
              }
              console.log(response)
            } catch (e) {
              if (e.status && e.status === Number(i18n.t('STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'))) {
                if (errors.detail) {
                  await Toast.show('error', errors.detail);
                }
              }
            }

          }
      }
    },

    chooseImage() {
      this.$refs.fileInput.click()
    },
    onSelectFile() {
      const input = this.$refs.fileInput
      const files = input.files
      const MAX_WIDTH = 1024;
      const MAX_HEIGHT = 1024;
      const MAX_SIZE = 2097152;
      if (files && files[0]) {
        if (files[0].size > MAX_SIZE) {
          Toast.show('warning', i18n.t('roles.big_size_image'));
          return
        }
        const reader = new FileReader();
        reader.readAsDataURL(files[0])
        reader.onload = e => {
          let img = new Image();
          let valid_img = true;
          img.src = e.target.result;
          img.onload = () => {
            if (img.width > MAX_WIDTH || img.height > MAX_HEIGHT ) {
              valid_img = false;
              this.isChooseImage = false;
              Toast.show('warning', i18n.t('roles.width_height_image', { width: MAX_WIDTH, height: MAX_HEIGHT }));
            } else {
              this.isChooseImage = true;
              this.product.product_image = e.target.result
            }
          }
        }
      }
    },

    clearFile () {
      this.isChooseImage = false;
      this.product.product_image = null;
      this.$refs.fileInput.value = null;
    }
  }
}
</script>

<style scoped>
.base-image-input {
  display: block;
  width: 200px;
  height: 200px;
  cursor: pointer;
  background-size: cover;
  background-position: center center;
}
.placeholder {
  background: #F0F0F0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #333;
  font-size: 18px;
  font-family: Helvetica;
}
.placeholder:hover {
  background: #E0E0E0;
}
.file-input {
  display: none;
}
</style>